<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Cache;
use SysvMessageQueue;

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
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 */
final class GDT_Hook extends GDT
{

	# Hook cache key
    final public const CACHE_KEY = 'HOOKS.GDOv7';

	/**
	 * @var int Num total calls.
	 */
	public static int $CALLS = 0;

	/**
	 * @var int Num IPC calls
	 */
	public static int $IPC_CALLS = 0;

	# Performance counter
	/**
	 * @var string[]
	 */
	private static ?array $CACHE = null;

	private static ?SysvMessageQueue $QUEUE = null;

	###########
	### GDT ###
	###########
	public string $event;

	public array $eventArgs;

	public bool $ipc = false;

	public static function clearCache(): void
	{
		self::$CACHE = null;
	}

	public static function callHook(string $event, ...$args): ?GDT
	{
		return self::call($event, false, $args);
	}

	/**
	 * Call a hook.
	 * Only registered modules are called since 6.10.6
	 */
	private static function call(string $event, bool $ipc, array $args): ?GDT
	{
		self::init();

		$response = self::callWebHooks($event, $args);

		if ($ipc && GDO_IPC && (GDO_IPC !== 'none'))
		{
			self::callIPCHooks($event, $args);
		}

		return $response;
	}

	/**
	 * Initialize the hooks from filesystem cache.
	 */
	public static function init(): void
	{
		if (!self::$CACHE)
		{
            if (Application::instance()->isCLI())
            {
                self::$CACHE = self::buildHookCache();
            }
			elseif ($hooks = Cache::fileGetSerialized(self::CACHE_KEY))
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

	/**
	 * Loop through all enabled modules and their methods.
	 * A methodname starting with hook adds to the hook table.
	 *
	 * @return array<string, string[]>
	 */
	private static function buildHookCache(): array
	{
		$cache = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$methods = get_class_methods($module);
			foreach ($methods as $methodName)
			{
                $start = Application::instance()->isCLI() ? 'clihook' : 'hook';
				if (str_starts_with($methodName, $start))
				{
					$event = substr($methodName, strlen($start));
					$cache[$event] ??= [];
					$cache[$event][] = $module->getName();
				}
			}
		}
		return $cache;
	}

	/**
	 * Call hook on all modules.
	 */
	private static function callWebHooks(string $event, array $args): ?GDT
	{
		# Count num calls up.
		self::$CALLS++; #PP#delete#

		$response = null;

		# Call hooks for this HTTP/www process.
		if ($moduleNames = self::getHookModuleNames($event))
		{
            $loader = ModuleLoader::instance();
            $method_name = Application::instance()->isCLI() ? "clihook{$event}" : "hook{$event}";
			foreach ($moduleNames as $moduleName)
			{
				if ($module = $loader->getModule($moduleName))
				{
					if ($result = call_user_func_array([$module, $method_name], $args))
					{
						$response = $response ?? GDT_Response::make();
						$response->addField($result);
					}
				}
			}
		}
		return $response;
	}

	###############
	### Private ###
	###############

	private static function getHookModuleNames(string $event): array
	{
		return self::$CACHE[$event] ?? GDT::EMPTY_ARRAY;
	}

	private static function callIPCHooks(string $event, array $args): void
	{
		#PP#begin#
		self::$IPC_CALLS++;
		if (GDO_IPC_DEBUG)
		{
			Logger::log('ipc', self::encodeHookMessage($event, $args));
		}
		#PP#end#
		if (GDO_IPC === 'db')
		{
			self::callIPCDB($event, $args);
		}
		elseif ($ipc = self::QUEUE())
		{
			self::callIPCQueue($ipc, $event, $args);
		}
	}

	private static function encodeHookMessage(string $event, array $args): string
	{
		return json_encode([
			'event' => $event,
			'args' => $args,
		]);
	}

	###########
	### IPC ###
	###########

	/**
	 * Sends a message to another process via the db.
	 */
	private static function callIPCDB(string $event, array $args): void
	{
		$args = self::encodeIPCArgs($args);
		GDO_Hook::blank([
			'hook_message' => self::encodeHookMessage($event, $args),
		])->insert();
	}

	/**
	 * Map GDO Objects to IDs.
	 * The IPC Service will refetch the Objects on their end.
	 */
	private static function encodeIPCArgs(array $args): array
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

	public static function QUEUE(): SysvMessageQueue|false
	{
		if (!self::$QUEUE)
		{
			$key = ftok(GDO_TEMP_PATH . 'ipc.socket', 'G');
			self::$QUEUE = msg_get_queue($key);
		}
		return self::$QUEUE;
	}

	private static function callIPCQueue($ipc, $event, array $args): void
	{
		try
		{
			$args = self::encodeIPCArgs($args);

			# Send to IPC
			$error = 0;
			$result = msg_send($ipc, 0x612, array_merge([$event], $args), true, false, $error);
			if ((!$result) || ($error))
			{
				Logger::logError("IPC msg_send($event) failed with code $error");
				msg_remove_queue(self::$QUEUE);
				self::$QUEUE = null;
			}
		}
		catch (\Throwable $ex)
		{
			Debug::debugException($ex);
		}
	}

	/**
	 * Call a hook with IPC(InterProcessCommunication) events.
	 * This will call the hook an all probably gdo daemons and server technologies for your setup.
	 * Fallback is an IPC emulation via Database/Filecache.
	 */
	public static function callWithIPC(string $event, ...$args): ?GDT
	{
		return self::call($event, true, $args);
	}

	public function hook(string $event, ...$args): self
	{
		$this->eventArgs = $args;
		return $this->event($event);
	}

	############
	### Init ###
	############

	public function event(string $event = null): self
	{
		$this->event = $event;
		return $this;
	}

	public function eventArgs(...$args): self
	{
		$this->eventArgs = $args;
		return $this;
	}

	public function ipc(bool $ipc = true): self
	{
		$this->ipc = $ipc;
		return $this;
	}

}
