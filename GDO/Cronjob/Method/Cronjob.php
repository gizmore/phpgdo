<?php
namespace GDO\Cronjob\Method;

use GDO\Admin\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use Exception;
use GDO\UI\GDT_HTML;
use GDO\User\GDO_User;

/**
 * Development aid for testing cronjobs.
 * 
 * @author gizmore
 */
class Cronjob extends MethodForm
{
	use MethodAdmin;
	
	public function isTransactional() { return false; }
	public function getPermission() { return 'admin'; }
	
	public function createForm(GDT_Form $form)
	{
		$form->actions()->addField(GDT_Submit::make()->label('btn_run_cronjob'));
		$form->addField(GDT_AntiCSRF::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		try
		{
			$user = GDO_User::current();
			GDO_User::setCurrent(GDO_User::system());
			
			ob_start();
			
			echo "<pre>";
			\GDO\Cronjob\Cronjob::run(true);
			echo "</pre>\n<br/>";
			
			return $this->renderPage()->addField(
			    GDT_HTML::withHTML(ob_get_contents()));
		}
		catch (Exception $ex)
		{
		    echo ob_get_contents();
			throw $ex;
		}
		finally
		{
			ob_end_clean();
			GDO_User::setCurrent($user);
		}
	}
	
}
