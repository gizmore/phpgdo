<?php
namespace GDO\Install\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Submit;
use GDO\Util\FileUtil;

/**
 * Optionally copy the main htaccess file.
 * @author gizmore
 */
final class CopyHTAccess extends MethodForm
{

	public function isUserRequired() : bool
	{
		return false;
	}

	public function getMethodTitle() : string
	{
		return t('install_title_9');
	}
	
	public function getMethodDescription() : string
	{
		return $this->getMethodTitle();
	}
	
	public function renderPage() : GDT
    {
        return GDT_Template::templatePHP('Install', 'page/copyhtaccess.php', ['form' => $this->getForm()]);
    }

    public function createForm(GDT_Form $form) : void
    {
        $form->actions()->addField(GDT_Submit::make()->label('copy_htaccess'));
    }
    
    public function formValidated(GDT_Form $form)
    {
    	$dest = GDO_PATH . '.htaccess';
    	if (!FileUtil::isFile($dest))
    	{
    		$src = GDO_PATH . '.htaccess.example';
	        copy($src, $dest);
    	}
        return parent::formValidated($form)->addField($this->renderPage());
    }
    
}
