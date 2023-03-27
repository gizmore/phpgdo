<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Core\Method\Stub;
use GDO\DB\Database;
use GDO\UI\GDT_Page;

/**
 * Application runtime data.
 * Global error methods.
 * Rendering mode.
 * Global Application time and microtime.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 * @see GDT_Page
 */
class Application extends GDT
{

	use WithVerb;
	use WithInstance;

	final public const EXIT_ERROR = -409;

	final public const EXIT_FATAL = -1;

	public static array $HREFS = [];

	################################
	### HREF COLLECTOR FOR TESTS ###
	################################
	public static int $TIME; #PP#delete#

	################
	### App Time ###
	################
	public static float $MICROTIME;
	/**
	 * HTTP Response Code.
	 */
	public static int $RESPONSE_CODE = 200;
	/**
	 * Current global rendering mode. @TODO make static for performance-
	 * For example switches from html to cell to form to table etc.
	 */
	public static int $MODE = GDT::RENDER_WEBSITE;
	/**
	 * Detected rendering mode for invocation.
	 */
	public static int $MODE_DETECTED = GDT::RENDER_WEBSITE;
	/**
	 * Ajax mode is website/html without the html boilerplate.
	 */
	public bool $ajax = false;
	/**
	 * Toggle CLI force mode (mostly for tests)
	 */
	public bool $cli = false;


	private array $themes;

	/**
	 * Call this at least.
	 */
	public static function init(): self
	{
		global $me;
		$me = Stub::make();
		return self::instance();
	}

	#################
	### HTTP Code ###
	#################

	/**
	 * Exit the application. On UnitTests we continue...
	 */
	public static function exit(int $code = 0): GDT
	{
		if (self::$INSTANCE->isUnitTests())
		{
			echo "The application would exit with code {$code}\n";
			flush();
			return GDT_Response::make();
		}
		die($code);
	}

	public function isUnitTests(): bool { return false; }

	public function isCLIOrUnitTest(): bool
	{
		return $this->isUnitTests() || $this->isCLI();
	}

	public function isIPC(): bool
	{
		return GDO_IPC !== 'none';
	}


	/**
	 * Set the HTTP response code.
	 */
	public static function setResponseCode(int $code): void
	{
		if ($code > self::$RESPONSE_CODE)
		{
			self::$RESPONSE_CODE = $code;
		}
	}

	public static function isCrash(): bool
	{
		return self::$RESPONSE_CODE >= 500;
	}

	public static function isSuccess(): bool
	{
		return self::$RESPONSE_CODE < 400;
	}

	#########################
	### Application state ###
	#########################

	public static function isDev(): bool { return self::isEnv('dev'); }

	private static function isEnv(string $env): bool { return def('GDO_ENV', 'dev') === $env; }

	public static function isTes(): bool { return self::isEnv('tes'); }

	# Render

	public static function isPro(): bool { return self::isEnv('pro'); }

	/**
	 * Detect the rendering output mode / format.
	 * GDO Applications currently support 6 formats, a 7th is planned: (GTK+App)
	 */
	public static function detectRenderMode(string $fmt): int
	{
		switch (strtoupper($fmt))
		{
			case 'TXT':
			case 'CLI':
				return GDT::RENDER_CLI;
			case 'IRC':
				return GDT::RENDER_IRC;
			case 'WS':
				return GDT::RENDER_BINARY;
			case 'PDF':
				return GDT::RENDER_PDF;
			case 'JSON':
				return GDT::RENDER_JSON;
			case 'XML':
				return GDT::RENDER_XML;
			case 'GTK':
				return GDT::RENDER_GTK;
			default:
				return GDT::RENDER_WEBSITE;
		}
	}

	/**
	 * @throws GDO_Exception
	 */
	public function __destruct()
	{
		parent::__destruct();
		if (class_exists('GDO\\Core\\Logger', false))
		{
			Logger::flush();
		}
	}

	/**
	 * Call when you create the next command in a loop.
	 * Optionally remove all input, when a form was sent and wants to get cleared.
	 */
	public function reset(): static
	{
		self::$RESPONSE_CODE = 200;
		$_FILES = [];
		GDT_Page::instance()->reset();
//		self::$MODE = self::$MODE_DETECTED;
		self::updateTime();
		return $this;
	}

	public static function updateTime(): void
	{
		self::time(microtime(true));
	}

	public static function time(float $time): void
	{
		self::$TIME = (int)$time;
		self::$MICROTIME = $time;
	}

	public function hasError(): bool
	{
		return self::isError();
	}

	public static function isError(): bool
	{
		return self::$RESPONSE_CODE >= 400;
	}

	# Install / Tests

	/**
	 * Perf headers as cheap as possible.
	 * Query count, memory usage, timing and call count.
	 */
	public function timingHeader(): void
	{
		$m1 = memory_get_peak_usage(true);
		$m2 = memory_get_peak_usage();
		hdr(sprintf('X-GDO-DB: %d', Database::$QUERIES));
		hdr(sprintf('X-GDO-MEM: %s', max([$m1, $m2])));
		hdr(sprintf('X-GDO-TIME: %.01fms', self::getRuntime() * 1000.0));
	}

	public static function getRuntime(): float
	{
		return microtime(true) - GDO_TIME_START;
	}

	# Env

	public function isTLS(): bool { return (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off'); }

	public function isAPI(): bool { return $this->isAjax() || $this->isJSON() || $this->isXML(); }

	public function isAjax(): bool { return $this->ajax; }

	public function isJSON(): bool { return self::$MODE_DETECTED === GDT::RENDER_JSON; }

	public function isXML(): bool { return self::$MODE_DETECTED === GDT::RENDER_XML; }

	public function isWebserver(): bool { return !$this->isCLI(); }

	###################
	### Render Mode ###
	###################

	public function isCLI(): bool { return $this->cli; }

	public function isWebsocket(): bool { return false; }

	public function isHTML(): bool { return self::$MODE_DETECTED >= 10; }

	public function isPDF(): bool { return self::$MODE_DETECTED === GDT::RENDER_PDF; }

	public function isGTK(): bool { return self::$MODE_DETECTED === GDT::RENDER_GTK; }

	############
	### Ajax ###
	############

	public function isInstall(): bool { return false; }

	/**
	 * Is a session handler supported?
	 */
	public function hasSession(): string
	{
		return module_enabled('Session');
	}

	################
	### CLI Mode ###
	################

	public function modeDetected(int $mode): self
	{
		self::$MODE_DETECTED = $mode;
		return $this->mode($mode);
	}

	/**
	 * Change current rendering mode.
	 * Optionally set detected mode to this.
	 */
	public function mode(int $mode): self
	{
		self::$MODE = $mode;
		return $this;
	}

	##############
	### Themes ###
	##############

	public function ajax(bool $ajax): self
	{
		$this->ajax = $ajax;
		return $this;
	}

	public function cli(bool $cli = true): self
	{
		if ($this->cli = $cli)
		{
			return $this->mode(GDT::RENDER_CLI);
		}
		return $this;
	}

	public function hasTheme(string $theme): bool
	{
		return isset($this->getThemes()[$theme]);
	}

	##################
	### JSON Input ###
	##################

	public function &getThemes(): array
	{
		if (!isset($this->themes))
		{
			$themes = explode(',', def('GDO_THEMES', 'default'));
			$this->themes = array_combine($themes, $themes);
		}
		return $this->themes;
	}

	/**
	 * Turn JSON requests into normal Requests.
	 */
	public function handleJSONRequests(): void
	{
		if (
			isset($_SERVER['CONTENT_TYPE']) &&
			($_SERVER['CONTENT_TYPE'] === 'application/json')
		)
		{
			$data = file_get_contents('php://input');
			if ($data = json_decode($data, true))
			{
				$_REQUEST = array_merge($_REQUEST, $data);
			}
		}
	}

}

# Init
Application::updateTime();
