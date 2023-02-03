<?php
namespace GDO\Core;

use GDO\Util\Strings;
use GDO\Language\Trans;
use GDO\UI\WithLabel;
use GDO\User\GDO_User;
use GDO\Util\FileUtil;
use GDO\Table\GDT_Filter;

/**
 * GDOv7 Template Engine as a GDT implementation.
 * 256 lines with theming and multi-lang support.
 *
 * - There are php and static file templates (for assets. These are both multi-lang capable, but it's better to store i18n in lang files (generator support)).
 * - Themes cascade similiar to css.
 * - @TODO get Sidebar Menu Ordering done.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 * @notin 3.0.4 - 4.9.0
 */
class GDT_Template extends GDT
{
	use WithGDO;
	use WithLabel; # so you can use GDT_Template in GDT_Table

	public function isTestable() : bool { return false; } # skip this class in tests

	/**
	 * Loaded themes. Name => Path
	 * @var string[string]
	 */
	public static array $THEMES = [];

	public static function themeNames() : array
	{
		return array_keys(self::$THEMES);
	}

	public static function registerTheme(string $theme, string $path) : void
	{
		self::$THEMES[$theme] = $path;
	}

	# ###########
	# ## Base ###
	# ###########
	public function defaultLabel(): self
	{
		return $this->labelNone();
	}

	public function htmlClass(): string
	{
		return parent::htmlClass() . "-{$this->templateModule}-" .
			Strings::rsubstrFrom(Strings::substrTo($this->templatePath, '.'), '/');
	}

	##############
	### Render ###
	##############
	public function render(): string
	{
		return $this->renderTemplate();
	}

	public function renderJSON()
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

	public function renderCLI(): string
	{
		return strip_tags($this->renderTemplate());
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return $this->renderTemplate($f);
	}

	public function renderTemplate($f = null) : string
	{
		$tVars = [
			'field' => $this,
			'f' => $f
		];
		$tVars = isset($this->templateVars) ? array_merge($this->templateVars, $tVars) : $tVars;
		return self::php($this->templateModule, $this->templatePath, $tVars);
	}

	# ###########
	# ## Type ###
	# ###########
	public string $templateModule;
	public string $templatePath;
	public array  $templateVars;

	public function template(string $moduleName, string $path, array $tVars = null) : self
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

	# #############
	# ## Engine ###
	# #############
	public static $CALLS = 0; # Performance counter

	/**
	 * Include a template for a user.
	 * Sets/Wraps locale ISO for the template call.
	 */
	public static function phpUser(GDO_User $user, string $moduleName, string $path, array $tVars = null) : string
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
			#PP#start#
			self::$CALLS++;
			$path = self::getPath($moduleName, $path);
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
			}
			include $path; # a hell of a bug is to supress errors here.
			return ob_get_contents();
		}
		catch (\Throwable $ex)
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
	 * Include a static file.
	 * Useful for localized asset loading.
	 */
	public static function file(string $moduleName, string $path): string
	{
		self::$CALLS++; #PP#delete#
		$path = self::getPath($moduleName, $path);
		return file_get_contents($path);
	}

	# ########################
	# ## Path substitution ###
	# ########################
	private static $PATHES = [];

	/**
	 * Get the Path for the GDO Theme Module Path and language.
	 */
	private static function getPath(string $moduleName, string $path): string
	{
// 		return self::getPathB($moduleName, $path);
// 	}

// 	private static function getPathB(string $moduleName, string $path): string
// 	{
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
			if (isset(self::$THEMES[$theme]))
			{
				foreach ($isos as $iso)
				{
					$path1 = $path12 . $iso . '.' . $path13;
					$path1 = self::$THEMES[$theme] . "/$moduleName/tpl/$path1";
					if (FileUtil::isFile($path1))
					{
						return $path1;
					}
				}
			}
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
			html("$moduleName/tpl/$path")
		]);
	}

}
