<?php
namespace GDO\Core;

use GDO\DB\Cache;

/**
 * Hooks can add messages to the IPC queue, which are/can be consumed by the websocket server.
 *
 * Hooks follow these convetions.
 * 
 * 1) The hook name is camel-case, e.g: 'UserAuthenticated'.
 * 2) The method name in your object has to be hookUserAuthenticated in this example.
 * 3) The hook name should include the module name, e.g. LoginSuccess, FriendsAccepted, CoreInitiated.
 *
 * @TODO: write an event system for games.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
final class GDT_Hook extends GDT
{
	# Hook cache key
	const CACHE_KEY = 'HOOKS.GDOv7';
	
	/**
	 * @var string[string]
	 */
	private static ?array $CACHE = null;
	
	public static function clearCache(): void
	{
		self::$CACHE = null;
	}
	
	# Performance counter
	public static int $CALLS = 0;     # Num Hook calls.
	public static int $IPC_CALLS = 0; # Num Hook calls with additional IPC overhead for CLI process sync.
	
	###########
	### GDT ###
	###########
	public function hook(string $event, ...$args) : self
	{
	    $this->eventArgs = $args;
	    return $this->event($event);
	}
	
	public string $event;
	public function event(string $event=null) : self
	{
	    $this->event = $event;
	    return $this->name($event);
	}
	
	public array $eventArgs;
	public function eventArgs(...$args) : self
	{
	    $this->eventArgs = $args;
	    return $this;
	}
	
	public bool $ipc = false;
	public function ipc(bool $ipc=true) : self
	{
	    $this->ipc = $ipc;
	    return $this;
	}
	
	##############
	### Render ###
	##############
// 	public function render() : string
// 	{
// 		$response = GDT_Response::newWith();
// 		$args = $this->eventArgs ? array_merge([$response], $this->eventArgs) : [$response];
// 		$res2 = self::call($this->event, $this->ipc, $args);
// 		return $response->addField($res2);
// 	}
	
// 	public function renderHTML() : string
// 	{
// 	    return $this->render()->renderHTML();
// 	}
	
	##############
	### Engine ###
	##############
	public static function callHook(string $event, ...$args)
	{
		return self::call($event, false, $args);
	}
	
	/**
	 * Call a hook with IPC(InterProcessCommunication) events.
	 * This will call the hook an all probably gdo daemons and server technologies for your setup.
	 * Fallback is an IPC emulation via Database/Filecache.
	 */
	public static function callWithIPC(string $event, ...$args)
	{
		return self::call($event, true, $args);
	}
	
	###############
	### Private ###
	###############
    /**
	 * Call a hook.
	 * Only registered modules are called since 6.10.6
	 */
	private static function call(string $event, bool $ipc, array $args) : ?GDT_Response
	{
		self::init();
		
		$response = self::callWebHooks($event, $args);
		
		if ($ipc && GDO_IPC && (GDO_IPC !== 'none'))
		{
			if ($r2 = self::callIPCHooks($event, $args))
			{
				if ($response === null)
				{
					$response = GDT_Response::make();
				}
				$response->addField($r2);
			}
		}
		return $response;
	}
	
	/**
	 * Call hook on all signed modules.
	 * @param string $event
	 * @param array $args
	 * @return GDT_Response
	 */
	private static function callWebHooks(string $event, array $args) : ?GDT_Response
	{
		# Count num calls up.
		self::$CALLS++;

		$response = GDT_Response::make();
		
		# Call hooks for this HTTP/www process.
		if ($moduleNames = self::getHookModuleNames($event))
		{
			$method_name = "hook$event";
			$loader = ModuleLoader::instance();
			foreach ($moduleNames as $moduleName)
			{
				if ($module = $loader->getModule($moduleName))
				{
					if ($module->isEnabled())
					{
						$callable = [$module, $method_name];
						$result = call_user_func_array($callable, $args);
						$response->addField($result);
					}
				}
			}
		}
		return $response;
	}
	
	private static function callIPCHooks(string $event, array $args)
	{
		self::$IPC_CALLS++;
		
		if (GDO_IPC_DEBUG)
		{
			Logger::log('ipc', self::encodeHookMessage($event, $args));
		}
		
		if (GDO_IPC === 'db')
		{
			self::callIPCDB($event, $args);
		}
		elseif ($ipc = self::QUEUE())
		{
			self::callIPCQueue($ipc, $event, $args);
		}
	}
	
	###########
	### IPC ###
	###########
	private static $QUEUE;
	public static function QUEUE()
	{
		if (!self::$QUEUE)
		{
			$key = ftok(GDO_TEMP_PATH . 'ipc.socket', 'G');
			self::$QUEUE = msg_get_queue($key);
		}
		return self::$QUEUE;
	}
	
	/**
	 * Map GDO Objects to IDs.
	 * The IPC Service will refetch the Objects on their end.
	 * @param array $args
	 */
	private static function encodeIPCArgs(array $args)
	{
		foreach ($args as $k => $arg)
		{
			if ($arg instanceof GDO)
			{
				$args[$k] = $arg->getID();
			}
		}
		return $args;
	}
	
	private static function callIPCQueue($ipc, $event, array $args)
	{
		$args = self::encodeIPCArgs($args);
		
		# Send to IPC
		$error = 0;
		$result = @msg_send($ipc, 0x612, array_merge([$event], $args), true, false, $error);
		if ( (!$result) || ($error) )
		{
			Logger::logError("IPC msg_send($event) failed with code $error");
			msg_remove_queue(self::$QUEUE);
			self::$QUEUE = null;
		}
	}

	/**
	 * Sends a message to another process via the db.
	 * @param string $event
	 * @param array $args
	 */
	private static function callIPCDB($event, array $args)
	{
		$args = self::encodeIPCArgs($args);
		GDO_Hook::blank([
			'hook_message' => self::encodeHookMessage($event, $args),
		])->insert();
	}
	
	private static function encodeHookMessage($event, array $args)
	{
		return json_encode([
			'event' => $event,
			'args' => $args,
		]);
	}
	
	############
	### Init ###
	############
	/**
	 * Initialize the hooks from filesystem cache.
	 */
	public static function init()
	{
		if (!isset(self::$CACHE))
		{
			if ($hooks = Cache::fileGetSerialized(self::CACHE_KEY))
			{
				self::$CACHE = $hooks;
			}
			else
			{
				self::$CACHE = self::buildHookCache();
				Cache::fileSetSerialized(self::CACHE_KEY, self::$CACHE);
			}
		}
	}
	
	private static function getHookModuleNames($event) : array
	{
		if (isset(self::$CACHE[$event]))
		{
			return self::$CACHE[$event];
		}
		return GDT::EMPTY_ARRAY;
	}
	
	/**
	 * Loop through all enabled modules and their methods.
	 * A methodname starting with hook adds to the hook table.
	 * 
	 * @return array<string, string[]>
	 */
	private static function buildHookCache() : array
	{
		$cache = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$classname = $module->gdoRealClassName();
			$methods = get_class_methods($classname);
			foreach ($methods as $methodName)
			{
				if (str_starts_with($methodName, 'hook'))
				{
					$event = substr($methodName, 4);
					if (!isset($cache[$event]))
					{
						$cache[$event] = [$module->getName()];
					}
					else
					{
						$cache[$event][] = $module->getName();
					}
				}
			}
		}
		return $cache;
	}

}
