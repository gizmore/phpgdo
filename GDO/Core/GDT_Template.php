<?php
namespace GDO\Core;

use GDO\Language\Trans;
use GDO\Table\GDT_Filter;
use GDO\UI\WithLabel;
use GDO\User\GDO_User;
use GDO\Util\FileUtil;
use GDO\Util\Strings;
use Throwable;

/**
 * GDOv7 Template Engine as a GDT implementation.
 * 256 lines with theming and multi-lang support.
 *
 * - There are php and static file templates (for assets. These are both multi-lang capable, but it's better to store i18n in lang files (generator support)).
 * - Themes cascade similiar to css.
 * - @TODO get Sidebar Menu Ordering done.
 *
 * @version 7.0.1
 * @since 3.0.0
 * @notin 3.0.4 - 4.9.0
 * @author gizmore
 */
class GDT_Template extends GDT
{

	use WithGDO;
	use WithLabel;

	# so you can use GDT_Template in GDT_Table

	/**
	 * Loaded themes. Name => Path
	 *
	 * @var string[string]
	 */
	public static array $THEMES = [];
	public static $CALLS = 0;
	public string $templateHeadModule;
	public string $templateHeadPath;

	# ###########
	# ## Base ###
	# ###########
	public ?array $templateHeadVars;
	public string $templateModule;

	##############
	### Render ###
	##############
	public string $templatePath;
	public array $templateVars;

	public static function themeNames(): array
	{
		return array_keys(self::$THEMES);
	}

	public static function registerTheme(string $theme, string $path): void
	{
		self::$THEMES[$theme] = $path;
	}

	/**
	 * Include a template for a user.
	 * Sets/Wraps locale ISO for the template call.
	 */
	public static function phpUser(GDO_User $user, string $moduleName, string $path, array $tVars = null): string
	{
		$old = Trans::$ISO;
		Trans::setISO($user->getLangISO());
		$result = self::php($moduleName, $path, $tVars);
		Trans::$ISO = $old;
		return $result;
	}

	/**
	 * Render a template via PHP include.
	 */
	public static function php(string $moduleName, string $path, array $tVars = null): string
	{
		try
		{
			ob_start();

			$path = self::getPath($moduleName, $path);

			#PP#start#
			self::$CALLS++;
			if (GDO_GDT_DEBUG)
			{
				$message = $path;
				if (GDO_GDT_DEBUG >= 2)
				{
					$message = Debug::backtrace($message, false);
				}
				Logger::log('tpl', $message);
			}
			#PP#end#
			if ($tVars)
			{
				foreach ($tVars as $__key => $__value)
				{
					# make tVars locals for the template.
					$$__key = $__value;
				}
//				unset($tVars);
//				unset($__key);
//				unset($__value);
			}

			include $path; # a hell of a bug is to supress errors here.
			return ob_get_contents();
		}
		catch (Throwable $ex)
		{
			$html = Debug::debugException($ex);
			if (GDO_ERROR_DIE)
			{
				die($html);
			}
			$partial = html(ob_get_contents());
			if (Application::$INSTANCE->isUnitTests())
			{
				ob_end_clean();
				echo $html;
				ob_start();
			}
			return $html . $partial;
		}
		finally
		{
			ob_end_clean();
		}
	}

	/**
	 * Get the Path for the GDO Theme Module Path and language.
	 */
	private static function getPath(string $moduleName, string $path): string
	{
		static $isosc = [];

		if (isset($isosc[Trans::$ISO]))
		{
			$isos = $isosc[Trans::$ISO];
		}
		else
		{
			$isos = array_unique([
				'_' . Trans::$ISO,
				'_' . GDO_LANGUAGE,
				'_en',
				'',
			]);
			$isosc[Trans::$ISO] = $isos;
		}


		# cut at dot.
		$path12 = Strings::rsubstrTo($path, '.', $path);
		$path13 = Strings::rsubstrFrom($path, '.', '');

		# Try themes first
		foreach (Application::$INSTANCE->getThemes() as $theme)
		{
//			if (isset(self::$THEMES[$theme]))
//			{
			foreach ($isos as $iso)
			{
				$path1 = $path12 . $iso . '.' . $path13;
				$path1 = self::$THEMES[$theme] . "/$moduleName/tpl/$path1";
				if (FileUtil::isFile($path1))
				{
					return $path1;
				}
			}
//			}
		}

		foreach ($isos as $iso)
		{
			$path1 = $path12 . $iso . '.' . $path13;
			$path1 = GDO_PATH . "GDO/$moduleName/tpl/$path1";
			if (FileUtil::isFile($path1))
			{
				return $path1;
			}
		}

		throw new GDO_Error('err_missing_template', [
			html("$moduleName/tpl/$path"),
		]);
	}

	#####################
	### Template Head ###
	#####################

	/**
	 * Include a static file.
	 * Useful for localized asset loading.
	 */
	public static function file(string $moduleName, string $path): string
	{
		self::$CALLS++; #PP#delete#
		$path = self::getPath($moduleName, $path);
		return file_get_contents($path);
	}

	public function isTestable(): bool { return false; }

	public function defaultLabel(): self
	{
		return $this->labelNone();
	}

	public function htmlClass(): string
	{
		return parent::htmlClass() . "-{$this->templateModule}-" .
			Strings::rsubstrFrom(Strings::substrTo($this->templatePath, '.'), '/');
	}

	public function render(): array|string|null
	{
		return $this->renderTemplate();
	}

	# ###########
	# ## Type ###
	# ###########

	public function renderTemplate($f = null): string
	{
		$tVars = [
			'field' => $this,
			'f' => $f,
		];
		$tVars = isset($this->templateVars) ? array_merge($this->templateVars, $tVars) : $tVars;
		return self::php($this->templateModule, $this->templatePath, $tVars);
	}

	public function renderJSON(): array|string|null
	{
		return null;
	}

	public function renderHTML(): string
	{
		return $this->renderTemplate();
	}

	public function renderForm(): string
	{
		return $this->renderTemplate();
	}

	# #############
	# ## Engine ###
	# #############

	public function renderCLI(): string
	{
		return strip_tags($this->renderTemplate());
	} # Performance counter

	public function renderFilter(GDT_Filter $f): string
	{
		return $this->renderTemplate($f);
	}

	public function templateHead(string $module, string $path, array $vars = null): self
	{
		$this->templateHeadModule = $module;
		$this->templateHeadPath = $path;
		$this->templateHeadVars = $vars;
		return $this;
	}

	public function renderTHead(): string
	{
		if (isset($this->templateHeadPath))
		{
			return self::php($this->templateHeadModule, $this->templateHeadPath, $this->templateHeadVars);
		}
		return GDT::EMPTY_STRING;
	}

	# ########################
	# ## Path substitution ###
	# ########################
//	private static $PATHES = [];

	public function template(string $moduleName, string $path, array $tVars = null): self
	{
		$this->templateModule = $moduleName;
		$this->templatePath = $path;
		if ($tVars)
		{
			$this->templateVars = $tVars;
		}
		else
		{
			unset($this->templateVars);
		}
		return $this;
	}

}
