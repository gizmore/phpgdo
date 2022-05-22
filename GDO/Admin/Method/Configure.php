<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\GDO_Module;
use GDO\Core\GDO_ModuleVar;
use GDO\Core\GDT_Module;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Version;
use GDO\DB\Cache;
use GDO\File\GDT_Path;
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
	
	private ?GDO_Module $configModule;
	
	public function getPermission() : ?string { return 'admin'; }
	public function showInSitemap() { return false; }
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_Module::make('module')->notNull(),
	    ];
	}
	
	/**
	 * Get the config module.
	 */
	public function paramModule() :GDO_Module
	{
		if (!isset($this->configModule))
		{
			$this->configModule = $this->gdoParameterValue('module');
		}
		return $this->configModule;;
	}
	
	public function execute()
	{
	    # Response
		$response = GDT_Response::make();
		
		# Response for install and configure
		if ($descr = $this->paramModule()->getModuleDescription())
		{
			$panelDescr = GDT_Panel::make()->textRaw($descr);
			$response->addField($panelDescr);
		}
		
		# Response for install panel
		$response->addField(Install::make()->executeWithInit());
		
		# Configuration if installed
		if ($this->paramModule()->isPersisted())
		{
			$response->addField(parent::execute()); # configure
		}
		
		# Respond
		return $response;
	}
	
	public function getTitle()
	{
        return t('ft_admin_configure', [$this->paramModule()->displayName()]);
	}
	
	public function getDescription()
	{
        return t('mdescr_admin_configure', [$this->paramModule()->displayName()]);
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$mod = $this->paramModule();
		$deps = Installer::getDependencyModules($mod->getName());
		$deps = array_filter($deps, function(GDO_Module $m) use ($mod) {
			return $m->getName() !== $mod->getName() AND !$m->isCoreModule();
		});
		$deps = array_map(function(GDO_Module $m) { return $m->getName(); }, $deps);
		$deps = array_map(function($nam) {
		    $link = href('Admin', 'Configure', "&module=".urlencode($nam));
		    $link = sprintf('<a href="%s">%s</a>', $link, html($nam));
		    return module_enabled($nam) ?
		    '<span class="dependency_ok">' . $link . '</span>' :
		    '<span class="dependency_ko">' . $link . '</span>';
		}, $deps);
		
		if (count($deps))
		{
		    $form->info(t('info_module_deps', [Arrays::implodeHuman($deps)]));
		}
		
		$form->addField(GDT_Name::make('module_name')->writable(false));
		$form->addField(GDT_Path::make('module_path')->writable(false)->initial($mod->filePath()));
		$c = GDT_Container::make('versions')->horizontal(false);
		$c->addField(GDT_Version::make('module_version')->writable(false));
		$c->addField(GDT_Version::make('version_available')->writable(false)->initial($mod->module_version));
		$form->addField($c->flex());
		$form->withGDOValuesFrom($this->configModule);
		if ($config = $mod->getConfigCache())
		{
			$form->addField(GDT_Divider::make('div1')->label('form_div_config_vars'));
			foreach ($config as $gdt)
			{
				$gdt->label('cfg_' . $gdt->name);
				$key = 'cfg_tt_' . $gdt->name;
				if (Trans::hasKey($key))
				{
					$gdt->tooltip($key);
				}
				$gdt->focusable(false);
				$form->addField($gdt); #->var($mod->getConfigVar($gdt->name)));
			}
		}
		$form->actions()->addField(GDT_Submit::make()->label('btn_save'));
		$form->addField(GDT_AntiCSRF::make());
		$form->action($this->href("&module=".$this->configModule->getName()));
	}
	
	public function formValidated(GDT_Form $form)
	{
		$mod = $this->paramModule();
		
		# Update config
		$info = [];
		$moduleVarsChanged = false;
		foreach ($form->getFields() as $gdt)
		{
// 			if ($gdt->hasChanged() && $gdt->writable && $gdt->editable)
			if ($gdt->hasChanged() && $gdt->isWritable())
			{
				$info[] = '<br/>';
				GDO_ModuleVar::createModuleVar($mod, $gdt);
				$info[] = t('msg_modulevar_changed',
				    [$gdt->renderLabel(),
				        $gdt->displayVar($gdt->initial),
				        $gdt->display()]);
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
		return $this->message('msg_module_saved', [implode('', $info)])->addField($this->renderPage());
	}

}
