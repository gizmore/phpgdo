<?php
declare(strict_types=1);
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\Application;
use GDO\Core\GDO_ArgError;
use GDO\Core\GDO_Module;
use GDO\Core\GDO_ModuleVar;
use GDO\Core\GDT;
use GDO\Core\GDT_Field;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Module;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Path;
use GDO\Core\GDT_Tuple;
use GDO\Core\GDT_Version;
use GDO\DB\Cache;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Install\Installer;
use GDO\Language\Trans;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Panel;
use GDO\UI\TextStyle;
use GDO\Util\Arrays;

/**
 * Configure a module.
 *
 * @version 7.0.3
 * @since 3.4.0
 * @author gizmore
 */
class Configure extends MethodForm
{

	use MethodAdmin;


	public function isTrivial(): bool
	{
		return false;
	}


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

	/**
	 * @throws GDO_ArgError
	 */
	public function execute(): GDT
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
		if ($mod->isPersisted())
		{
			$response->addField(parent::execute()); # configure
		}
		elseif ($text = $this->getDependencyText())
		{
			$response->addField(GDT_Panel::make()->text('info_module_deps', [$text]));
		}

		if ($text = $this->getFriendencyText())
		{
			$response->addField(GDT_Panel::make()->text('info_module_freps', [$text]));
		}

		# Respond
		return $response;
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function configModule(): GDO_Module
	{
		return $this->gdoParameterValue('module');
	}

	/**
	 * @throws GDO_ArgError
	 */
	private function getDependencyText(): string
	{
		$mod = $this->configModule();
		$deps = Installer::getDependencyNames($mod->getName());
		return $this->getDepsText($deps);
	}

	private function getDepsText(array $deps): string
	{
		$deps = array_map(
			function ($nam)
			{
				$link = href('Admin', 'Configure',
					'&module=' . urlencode($nam));
				$link = sprintf('<a href="%s">%s</a>', $link,
					html($nam));
				return module_enabled($nam) ? '<span class="dependency_ok">' .
					$link . '</span>' : '<span class="dependency_ko">' .
					$link . '</span>';
			}, $deps);

		return Arrays::implodeHuman($deps);
	}

	/**
	 * @throws GDO_ArgError
	 */
	private function getFriendencyText(): string
	{
		$mod = $this->configModule();
		$deps = Installer::getFriendencyNames($mod->getName());
		return $this->getDepsText($deps);
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function getMethodTitle(): string
	{
		return t('mt_admin_configure',
			[
				$this->configModule()->renderName(),
			]);
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function getMethodDescription(): string
	{
		return t('md_admin_configure',
			[
				$this->configModule()->renderName(),
			]);
	}

	/**
	 * @throws GDO_ArgError
	 */
	protected function createForm(GDT_Form $form): void
	{
		$mod = $this->configModule();
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
				if ($gdt instanceof GDT_Field)
				{
					$key = 'cfg_' . $gdt->name;
					if (Trans::hasKey($key))
					{
						$gdt->label($key);
					}
					$key = 'tt_cfg_' . $gdt->name;
					if (Trans::hasKey($key))
					{
						$gdt->tooltip($key);
					}
				}
				$form->addField($gdt)->var($mod->getConfigVar($gdt->name));
			}
		}
		$form->actions()->addField(GDT_Submit::make()->label('btn_save'));
		$form->addField(GDT_AntiCSRF::make());
		$form->action($this->href("&module={$mod->getName()}"));
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function formValidated(GDT_Form $form): GDT
	{
		$mod = $this->configModule();

		# Update config
		$info = [];
		$varsChanged = false;
		foreach ($form->getAllFields() as $gdt)
		{
			if (($gdt instanceof GDT_Field) &&
				(!$gdt->isHidden()) &&
				($gdt->isWriteable()) &&
				($gdt->hasChangedFromDefault()))
			{
				if (count($info))
				{
					$info[] = Application::$INSTANCE->isCLIOrUnitTest() ? ' - ' : '<br/>';
				}
				GDO_ModuleVar::createModuleVar($mod, $gdt);
				$info[] = t('msg_modulevar_changed',
					[
						$gdt->renderLabel(),
						TextStyle::italic($gdt->displayVar($gdt->getInitial())),
						TextStyle::italic($gdt->displayVar($gdt->getVar())),
					]);
				$varsChanged = true;
			}
		}

		if ($varsChanged)
		{
			Cache::flush();
			GDT_Hook::callWithIPC('ModuleVarsChanged', $mod);
		}

		# Announce
		return $this->message('msg_module_saved',
			[
				trim(implode("\n", $info)),
			])->addField($this->renderPage());
	}

}
