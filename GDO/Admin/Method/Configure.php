<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\GDO_Module;
use GDO\Core\GDO_ModuleVar;
use GDO\Core\GDT_Module;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Version;
use GDO\DB\Cache;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Divider;
use GDO\Language\Trans;
use GDO\UI\GDT_Panel;
use GDO\Install\Installer;
use GDO\Util\Arrays;
use GDO\UI\GDT_Container;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Tuple;

/**
 * Configure a module.
 *
 * @TODO: Write a unit test that does configure on each module.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 3.4.0
 */
class Configure extends MethodForm
{
	use MethodAdmin;

	private GDO_Module $configModule;

	public function getPermission(): ?string
	{
		return 'admin';
	}

	public function showInSitemap() : bool
	{
		return false;
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Module::make('module')->uninstalled()->notNull(),
		];
	}

	/**
	 * Get the config module.
	 */
	public function configModule(): GDO_Module
	{
		if ( !isset($this->configModule))
		{
			$this->configModule = $this->gdoParameterValue('module');
		}
		return $this->configModule;
	}

	public function execute()
	{
		# Response
		$response = GDT_Tuple::make();

		# Response for install and configure
		if ($descr = $this->configModule()->getModuleDescription())
		{
			$panelDescr = GDT_Panel::make()->textRaw($descr);
			$response->addField($panelDescr);
		}

		# Response for install panel
		$install = Install::make()->inputs($this->getInputs());
		$response->addField($install->executeWithInit());

		# Configuration if installed
		if ($this->configModule()->isPersisted())
		{
			$response->addField(parent::execute()); # configure
		}
		else
		{
			if ($text = $this->getDependencyText())
			{
				$response->addField(
					GDT_Panel::make()->text('info_module_deps', [$text]));
			}
		}
		
		# Respond
		return $response;
	}

	public function getMethodTitle() : string
	{
		return t('ft_admin_configure',
			[
				$this->configModule()->renderName()
			]);
	}

	public function getMethodDescription() : string
	{
		return t('mdescr_admin_configure',
			[
				$this->configModule()->renderName()
			]);
	}

	private function getDependencyText(): string
	{
		$mod = $this->configModule();
		$deps = Installer::getDependencyModules($mod->getName());
		$deps = array_filter($deps,
			function (GDO_Module $m=null) use ($mod)
			{
				if ( (!$m) || ($m->isCoreModule()) )
				{
					return false;
				}
				return $m->getName() !== $mod->getName();
			});
		$deps = array_map(
			function (GDO_Module $m)
			{
				return $m->getName();
			}, $deps);
		$deps = array_map(
			function ($nam)
			{
				$link = href('Admin', 'Configure',
					"&module=" . urlencode($nam));
				$link = sprintf('<a href="%s">%s</a>', $link,
					html($nam));
				return module_enabled($nam) ? '<span class="dependency_ok">' .
				$link . '</span>' : '<span class="dependency_ko">' .
				$link . '</span>';
			}, $deps);

		return Arrays::implodeHuman($deps);
	}

	public function createForm(GDT_Form $form): void
	{
		$mod = $this->configModule();

		if ($deps = $this->getDependencyText())
		{
			$form->text('info_module_deps', [
				$deps
			]);
		}

		$form->addField(
			GDT_Name::make('module_name')->initial($mod->getName())->writeable(false));
		$form->addField(
			GDT_String::make('module_path')->writeable(false)
				->initial($mod->filePath()));
		$c = GDT_Container::make('versions')->horizontal(false);
		$c->addField(
			GDT_Version::make('module_version')->gdo($mod)->writeable(
				false));
		$c->addField(
			GDT_Version::make('version_available')->writeable(
				false)
				->initial($mod->version));
		$form->addField($c->flex());
		$form->inputs($this->configModule->getGDOVars());
		if ($config = $mod->getConfigCache())
		{
			$form->addField(
				GDT_Divider::make()->label(
					'form_div_config_vars'));
			foreach ($config as $gdt)
			{
				$gdt->label('cfg_' . $gdt->name);
				$key = 'cfg_tt_' . $gdt->name;
				if (Trans::hasKey($key))
				{
					$gdt->tooltip($key);
				}
				// $gdt->focusable(false);
				$form->addField($gdt); # ->var($mod->getConfigVar($gdt->name)));
			}
		}
		
		$form->actions()->addField(
			GDT_Submit::make()->label('btn_save'));
		$form->addField(GDT_AntiCSRF::make());
		$form->action($this->href("&module=" . $mod->getName()));
	}

	public function formValidated(GDT_Form $form)
	{
		$mod = $this->configModule();

		# Update config
		$info = [];
		$moduleVarsChanged = false;
		foreach ($form->getAllFields() as $gdt)
		{
			// if ($gdt->hasChanged() && $gdt->writeable && $gdt->editable)
			if ((!$gdt->isHidden()) && $gdt->isWriteable() && $gdt->hasChanged())
			{
				$info[] = '<br/>';
				GDO_ModuleVar::createModuleVar($mod, $gdt);
				$info[] = t('msg_modulevar_changed',
					[
						$gdt->renderLabel(),
						$gdt->displayVar($gdt->initial),
						$gdt->displayVar($gdt->getVar()),
					]);
				$moduleVarsChanged = true;
			}
		}

		if ($moduleVarsChanged)
		{
			Cache::flush();
			Cache::fileFlush();
			GDT_Hook::callWithIPC('ModuleVarsChanged', $mod);
		}

		# Announce
		return $this->message('msg_module_saved',
			[
				implode('', $info)
			])->addField($this->renderPage());
	}

}
