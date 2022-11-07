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
use GDO\Core\GDT_Tuple;
use GDO\Core\GDT_Path;

/**
 * Configure a module.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 3.4.0
 */
class Configure extends MethodForm
{
	use MethodAdmin;

	public function getPermission(): ?string
	{
		return 'admin';
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Module::make('module')->uninstalled()->notNull(),
		];
	}

	public function configModule(): GDO_Module
	{
		return $this->gdoParameterValue('module');
	}

	public function execute()
	{
		# Response
		$response = GDT_Tuple::make();
		
		$mod = $this->configModule();

		# Response for install and configure
		if ($descr = Installer::getModuleDescription($mod))
		{
			$panelDescr = GDT_Panel::make()->textRaw($descr);
			$response->addField($panelDescr);
		}

		# Response for install panel
		$install = Install::make()->inputs($this->inputs);
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
				$response->addField(GDT_Panel::make()->text('info_module_deps', [$text]));
			}
		}
		
		if ($text = $this->getFriendencyText())
		{
			$response->addField(GDT_Panel::make()->text('info_module_freps', [$text]));
		}
		
		# Respond
		return $response;
	}

	public function getMethodTitle() : string
	{
		return t('mt_admin_configure',
			[
				$this->configModule()->renderName()
			]);
	}

	public function getMethodDescription() : string
	{
		return t('md_admin_configure',
			[
				$this->configModule()->renderName()
			]);
	}
	
	private function getFriendencyText() : string
	{
		$mod = $this->configModule();
		$deps = Installer::getFriendencyModules($mod->getName());
		return $this->getDepsText($deps);
	}
	
	private function getDependencyText(): string
	{
		$mod = $this->configModule();
		$deps = Installer::getDependencyModuleNames($mod->getName());
		return $this->getDepsText($deps);
	}
	
	private function getDepsText(array $deps): string
	{
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
// 		if ($deps = $this->getFriendencyText())
// 		{
// 			$form->text('info_module_deps', [
// 				$deps
// 			]);
// 		}
		$form->addField(GDT_Name::make('module_name')->initial($mod->getModuleName())->writeable(false));
		$form->addField(GDT_Path::make('module_path')->writeable(false)->initial($mod->filePath()));
		$c = GDT_Container::make('versions')->horizontal();
		$c->addField($mod->gdoColumn('module_version')->writeable(false)->initial($mod->gdoVar('module_version')));
		$c->addField(GDT_Version::make('version_available')->writeable(false)->initial($mod->version));
		$form->addField($c);
		if ($config = $mod->getConfigCache())
		{
			$form->addField(
				GDT_Divider::make()->label(
					'form_div_config_vars'));
			foreach ($config as $gdt)
			{
				$gdt->label('cfg_' . $gdt->name);
				$key = 'tt_cfg_' . $gdt->name;
				if (Trans::hasKey($key))
				{
					$gdt->tooltip($key);
				}
				$form->addField($gdt)->var($mod->getConfigVar($gdt->name));
			}
		}
		$form->actions()->addField(GDT_Submit::make()->label('btn_save'));
		$form->addField(GDT_AntiCSRF::make());
		$form->action($this->href("&module={$mod->getName()}"));
	}
	
// 	private function resetConfig()
// 	{
// 		$mod = $this->configModule();
// 		foreach ($mod->getConfigCache() as $gdt)
// 		{
// 			$gdt->reset(true);
// 		}
// 	}
	
// 	public function formInvalid(GDT_Form $form)
// 	{
// 		$this->resetConfig();
// 		return parent::formInvalid($form);
// 	}

// 	public function afterExecute(): void
// 	{
// 		$this->resetConfig();
// 		parent::afterExecute();
// 	}
	
	public function formValidated(GDT_Form $form)
	{
		$mod = $this->configModule();

		# Update config
		$info = [];
		$moduleVarsChanged = false;
		foreach ($form->getAllFields() as $gdt)
		{
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
		
// 		$this->resetConfig();
		
		# Announce
		return $this->message('msg_module_saved',
			[
				implode("\n", $info)
			])->addField($this->renderPage());
	}

}
