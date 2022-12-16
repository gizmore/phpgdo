<?php
namespace GDO\Core\Method;

use GDO\Core\Module_Core;
use GDO\Mail\Mail;
use GDO\Net\GDT_Url;
use GDO\UI\MethodPage;
use GDO\User\GDO_User;

/**
 * Show a 403 page.
 * Send an email if opted-in.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.0
 */
final class NotAllowed extends MethodPage
{
	public function isSavingLastUrl() : bool
	{
		return false;
	}
	
	protected function isFileCacheEnabled() : bool
	{
		return false;
	}
	
	public function getMethodTitle() : string
	{
		return t('forbidden');
	}
	
	public function getMethodDescription() : string
	{
		return t('err_forbidden');
	}
	
	public function getTemplateName() : string
	{
		return 'page/403_page.php';
	}
	
	public function gdoParameters() : array
	{
		return [
			GDT_Url::make('url')->notNull()->allowInternal(),
		];
	}
	
	public function beforeExecute() : void
	{
		if (Module_Core::instance()->cfgMail403())
		{
			if (module_enabled('Mail'))
			{
				$this->send403Mails();
			}
		}
	}
	
	private function send403Mails() : void
	{
		foreach (GDO_User::admins() as $user)
		{
			$this->send403Mail($user);
		}
	}
	
	private function send403Mail(GDO_User $user) : void
	{
		$url = $this->gdoParameterVar('url');
		$mail = Mail::botMail();
		$mail->setSubject(t('mail_title_403', [
			sitename(), html($url)]));
		$tVars = [
			html($user->renderUserName()),
			sitename(),
			html($url),
			GDO_User::current()->renderUserName(),
		];
		$mail->setBody(t('mail_body_403', $tVars));
		$mail->sendToUser($user);
	}

}
