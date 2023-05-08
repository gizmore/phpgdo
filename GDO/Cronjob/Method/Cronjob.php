<?php
namespace GDO\Cronjob\Method;

use Exception;
use GDO\Admin\MethodAdmin;
use GDO\Core\GDT;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Success;
use GDO\User\GDO_User;

/**
 * Development aid for testing cronjobs.
 *
 * @author gizmore
 */
class Cronjob extends MethodForm
{

	use MethodAdmin;

	public function isTrivial(): bool
	{
		return false;
	}

	public function isTransactional(): bool { return false; }

	protected function createForm(GDT_Form $form): void
	{
		$form->actions()->addField(GDT_Submit::make()->label('btn_run_cronjob'));
		$form->addField(GDT_AntiCSRF::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		try
		{
			$user = GDO_User::current();
			GDO_User::setCurrent(GDO_User::system());

			ob_start();

			echo '<pre>';
			\GDO\Cronjob\Cronjob::run(true);
			echo "</pre>\n<br/>";

			return $this->renderPage()->addField(
				GDT_Success::make()->textRaw(ob_get_contents()));
		}
		catch (Exception $ex)
		{
			echo html(ob_get_contents());
			throw $ex;
		}
		finally
		{
			ob_end_clean();
			GDO_User::setCurrent($user);
		}
	}

	public function getPermission(): ?string { return 'admin'; }

}
