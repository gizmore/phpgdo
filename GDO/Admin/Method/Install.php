<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Module;
use GDO\DB\Cache;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Install\Installer;
use GDO\Core\ModuleLoader;
use GDO\UI\GDT_Button;
use GDO\Util\Strings;
use GDO\Util\Common;

/**
 * Install a module. Wipe a module. Enable and disable a module.
 * 
 * @TODO Automatic DB migration for GDO. triggered by re-install module.
 * 
 * @author gizmore
 * @version 6.10.7
 * @since 3.0.0
 */
class Install extends MethodForm
{
	use MethodAdmin;
	
	public function formName() { return 'form_install'; }
	public function getPermission() : ?string { return 'admin'; }
	public function beforeExecute() : void {} # hide tabs (multi method configure page fix)
	
	/**
	 * @var GDO_Module
	 */
	private $configModule;
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_Module::make('module')->uninstalled()->notNull(),
	    ];
	}
	
	public function onInit() : void
	{
	    $loader = ModuleLoader::instance();
		$moduleName = strtolower(Common::getRequestString('module', ''));
		$this->configModule = $loader->getModule($moduleName);
	}
	
	public function execute()
	{
		$buttons = ['install', 'reinstall', 'uninstall', 'enable', 'disable'];
		$form = $this->formName();
		foreach ($buttons as $button)
		{
			if (isset($_REQUEST[$form][$button]))
			{
				return $this->executeButton($button)->addField($this->renderPage());
			}
		}
		return $this->renderPage();
	}
	
	public function getTitle()
	{
	    if ($this->configModule)
	    {
	        return t('ft_admin_install', [$this->configModule->renderName()]);
	    }
	    else
	    {
	        return t('btn_install');
	    }
	}
	
	/**
	 * The 3 button install form.
	 * {@inheritDoc}
	 * @see \GDO\Form\MethodForm::createForm()
	 */
	public function createForm(GDT_Form $form) : void
	{
	    $form->action(href('Admin', 'Configure', '&module='.Common::getRequestString('module')));
	    
		$form->actions()->addField(GDT_Submit::make('install')->label('btn_install'));

		if ($this->configModule && $this->configModule->isInstalled())
		{
			$tables = $this->configModule->getClasses();
			$modules = empty($tables) ? t('enum_none') : implode(', ', array_map(function($t){return Strings::rsubstrFrom($t, '\\');}, $tables));
			$text = t('confirm_wipe_module', [$modules]);
			$form->actions()->addField(GDT_Submit::make('uninstall')->label('btn_uninstall')->attr('onclick', 'return confirm(\''.$text.'\')"'));
			$form->actions()->addField(GDT_Submit::make('reinstall')->label('btn_reinstall'));
			if ($this->configModule->isEnabled())
			{
			    $form->actions()->addField(GDT_Submit::make('disable')->label('btn_disable'));
			}
			else
			{
				$form->actions()->addField(GDT_Submit::make('enable')->label('btn_enable'));
			}
			
			if ($adminHREF = $this->configModule->href_administrate_module())
			{
			    $form->actions()->addField(GDT_Button::make('href_admin')->href($adminHREF));
			}
		}
		
		$form->addField(GDT_AntiCSRF::make());
	}
	
	###############
	### Execute ###
	###############
	public function executeButton($button)
	{
		$form = $this->getForm();
		if (!$form->validateForm())
		{
			return parent::formInvalid($form);
		}
		$response = call_user_func([$this, "execute_$button"]);
		Cache::flush();
		Cache::fileFlush();
		$this->resetForm();
		return $response;
	}
	
	public function execute_install()
	{
		Installer::installModule($this->configModule);
		$this->configModule->saveVar('module_enabled', '1');
		return $this->message('msg_module_installed', [$this->configModule->getName()]);
	}
	
	public function execute_reinstall()
	{
		Installer::installModule($this->configModule, true);
		return $this->message('msg_module_installed', [$this->configModule->getName()]);
	}
	
	public function execute_uninstall()
	{
		Installer::dropModule($this->configModule);
		return $this->message('msg_module_uninstalled', [$this->configModule->getName()]);
	}
	
	public function execute_enable()
	{
		$this->configModule->saveVar('module_enabled', '1');
		return $this->message('msg_module_enabled', [$this->configModule->getName()]);
	}

	public function execute_disable()
	{
		$this->configModule->saveVar('module_enabled', '0');
		return $this->message('msg_module_disabled', [$this->configModule->getName()]);
	}
	
}
