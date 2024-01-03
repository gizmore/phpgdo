<?php
declare(strict_types=1);
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDO_ArgError;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Field;
use GDO\Core\GDT_Module;
use GDO\Core\GDT_Tuple;
use GDO\Core\Website;
use GDO\DB\Cache;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Install\Installer;
use GDO\UI\GDT_Button;
use GDO\Util\Strings;

/**
 * Install a module. Wipe a module. Enable and disable a module.
 *
 * @TODO Automatic DB migration for GDO. triggered by re-install module.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 */
class Install extends MethodForm
{

	use MethodAdmin;

    public function isCLI(): bool { return true; }

	public function isTrivial(): bool { return false; }

	public function getFormName(): string
	{
		return 'form_install';
	}

	public function gdoParameters(): array
	{
		return [
			# Also modules that are not installed are allowed
			GDT_Module::make('module')->installed()->uninstalled()->notNull(),
            GDT_Submit::make('uninstall'),
		];
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function execute(): GDT
	{
		$buttons = ['install', 'reinstall', 'uninstall', 'enable', 'disable', 'config_check', 'config_fix'];
		foreach ($buttons as $btn)
		{
			if ($button = $this->gdoParameter($btn, false, false))
			{
				if ($button->hasInput())
				{
					return GDT_Tuple::makeWith(
						$this->executeButton($btn),
						$this->renderPage());
				}
			}
		}
		return $this->renderPage();
	}

	public function executeButton($button)
	{
		$response = call_user_func([$this, "execute_$button"]);
		Cache::flush();
		$this->resetForm();
		return $response;
	}

	/**
	 * Install is called from Configure.
	 * No double Tabs please.
	 */
	public function onRenderTabs(): void {}

	public function execute_install()
	{
		$mod = $this->configModule();
		Installer::installModuleWithDependencies($mod);
		$mod->saveVar('module_enabled', '1');
		return $this->message('msg_module_installed', [$mod->getName()]);
	}

	public function configModule(): GDO_Module
	{
		return $this->gdoParameterValue('module');
	}

	public function execute_reinstall()
	{
		$mod = $this->configModule();
		Installer::installModuleWithDependencies($mod, true);
		return $this->message('msg_module_installed', [$mod->getName()]);
	}

	public function execute_uninstall()
	{
		$mod = $this->configModule();
		Installer::dropModule($mod);
		return $this->message('msg_module_uninstalled', [$mod->getName()]);
	}

	public function getMethodTitle(): string
	{
		if ($module = $this->configModule())
		{
			return t('mt_admin_install', [$module->renderName()]);
		}
		else
		{
			return t('btn_install');
		}
	}

	public function execute_enable()
	{
		$mod = $this->configModule();
		$mod->saveVar('module_enabled', '1');
		return $this->message('msg_module_enabled', [$mod->getName()]);
	}

	public function execute_disable()
	{
		$mod = $this->configModule();
		$mod->saveVar('module_enabled', '0');
		return $this->message('msg_module_disabled', [$mod->getName()]);
	}

	public function execute_check_config(): GDT
	{
		return $this->checkConfiguration($this->configModule());
	}

	public function execute_fix_config(): GDT
	{
		return $this->checkConfiguration($this->configModule(), true);
	}

	/**
	 * Check all modules user settings and configrations.
	 * Optionally fix them by removing the vars.
	 */
	public function checkConfiguration(GDO_Module $module, bool $fix = false): GDT
	{
		$checked = 0;
		$entries = 0;
		$errors = 0;
		$fixed = 0;

		foreach ($module->getConfigCache() as $gdt)
		{
			if ($gdt instanceof GDT_Field)
			{
				$checked++;
				if (!$this->checkConfigGDT($module, $gdt, $fix))
				{
					$errors++;
					if ($fix)
					{
						$fixed++;
					}
				}
				if ($gdt->hasChanged())
				{
					$entries++;
				}
			}
		}

//		# @TODO Implement for settings. need to walk the user_setting tables!
//		foreach ($module->getSettingsCache() as $gdt)
//		{
//			if ($gdt instanceof GDT_Field)
//			{
//				# check for all entries!
//			}
//		}

		if ($errors)
		{
			return $this->error('err_mod_config', [$module->gdoHumanName(), $checked, $errors, $entries, $fixed]);
		}

		return $this->message('msg_mod_config_ok', [$module->gdoHumanName(), $checked, $entries]);
	}

	/**
	 * Check, and optionally fix a module config var.
	 */
	private function checkConfigGDT(GDO_Module $module, GDT $gdt, bool $fix): bool
	{
		if (!$gdt->validated())
		{
			if (!$fix)
			{
				#'The %s module value `%s` for %s is invalid: %s - %s.
				$args = [
					$module->gdoHumanName(),
					$gdt->renderVar(),
					$gdt->gdoHumanName(),
					$gdt->renderError(),
					$gdt->gdoExampleVars(),
				];
				Website::error($module->gdoHumanName(), 'err_mod_config_error', $args);
			}
			else
			{
				$old = $gdt->renderVar();
				$module->removeConfigVar($gdt->getName());
				$args = [
					$module->gdoHumanName(),
					$old,
					$gdt->gdoHumanName(),
					$gdt->renderError(),
					$gdt->displayVar($gdt->getInitial()),
				];
				Website::message($module->gdoHumanName(), 'msg_mod_config_fixed', $args);
			}
			return false;
		}
		return true;
	}

	/**
	 * The 4 button install form.
	 */
	protected function createForm(GDT_Form $form): void
	{
		$mod = $this->configModule();

		$form->action(href('Admin', 'Configure', '&module=' . $mod->getName()));

		$form->actions()->addField(GDT_Submit::make('install')->label('btn_install'));

		if ($mod && $mod->isInstalled())
		{
			$tables = $mod->getClasses();
			$modules = empty($tables) ? t('enum_none') : implode(', ', array_map(function ($t) { return Strings::rsubstrFrom($t, '\\'); }, $tables));
			$text = t('confirm_wipe_module', [$modules]);
			$form->actions()->addField(GDT_Submit::make('uninstall')->label('btn_uninstall')->attr('onclick', 'return confirm(\'' . $text . '\')"'));
			$form->actions()->addField(GDT_Submit::make('reinstall')->label('btn_reinstall'));
			if ($mod->isEnabled())
			{
				$form->actions()->addField(GDT_Submit::make('disable')->label('btn_disable'));
			}
			else
			{
				$form->actions()->addField(GDT_Submit::make('enable')->label('btn_enable'));
			}

			if ($adminHREF = $mod->href_administrate_module())
			{
				$form->actions()->addField(GDT_Button::make('href_admin')->href($adminHREF));
			}
		}
		else
		{
			$form->titleRaw($this->getMethodTitle());
		}

		$form->addField(GDT_AntiCSRF::make());
	}


}
