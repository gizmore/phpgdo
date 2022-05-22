<?php
namespace GDO\Install\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Install\Config;
use GDO\Util\FileUtil;
use GDO\Core\GDT_Template;
use GDO\DB\Database;
use GDO\Core\GDO_Exception;
use GDO\Core\Website;
use GDO\File\GDT_Path;
use GDO\Form\GDT_Hidden;

/**
 * Create a GDO config with this form.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 */
class Configure extends MethodForm
{
	public function isEnabled() : bool { return true; }
	
	public function gdoParameters() : array
    {
        return [
            GDT_Path::make('filename')->initial('config.php'),
            GDT_Hidden::make('step')->initial('3'),
          ];
    }
    
    public function cfgConfigName() { return $this->gdoParameterVar('filename'); }
    
	public function configPath()
	{
		return GDO_PATH . 'protected/' . $this->cfgConfigName();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		foreach (Config::fields() as $gdt)
		{
			$form->addField($gdt);
		}
		$form->actions()->addField(GDT_Submit::make('save_config')->onclick([$this, 'onSaveConfig']));
// 		if (FileUtil::isFile($this->configPath()))
		{
			$form->actions()->addField(GDT_Submit::make('test_config')->onclick([$this, 'onTestConfig']));
		}
	}

	public function onSaveConfig()
	{
		$form = $this->getForm();
		$content = GDT_Template::php('Install', 'config.php', ['form' => $form]);
		FileUtil::createDir(dirname($this->configPath()));
		file_put_contents($this->configPath(), $content);
		return Website::redirectMessage('msg_config_written', [html($this->cfgConfigName())], Config::hrefStep(3));
	}
	
	public function onTestConfig()
	{
		if (GDO_DB_ENABLED)
	    {
    		$db = new Database(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, GDO_DB_NAME, false);
    		try
    		{
    			$db->getLink();
    		}
    		catch (GDO_Exception $ex)
    		{
    			return $this->error('err_db_connect')->addField($this->renderPage());
    		}
	    }
	    return $this->message('install_config_boxinfo_success', [Config::linkStep(4)]);
	}
	
}
