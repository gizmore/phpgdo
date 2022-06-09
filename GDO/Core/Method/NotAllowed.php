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
 * @version 7.0.0
 * @since 7.0.0
 */
final class NotAllowed extends MethodPage
{
	public function getMethodTitle() : string
	{
		return t('forbidden');
	}
	
	public function getMethodDescription() : string
	{
		return t('err_forbidden');
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
			$this->send403Mails();
		}
	}
	
	public function execute()
	{
		return $this->pageTemplate('403_page');
	}
	
	private function send403Mails() : void
	{
		foreach (GDO_User::staff() as $user)
		{
			$this->send403Mail($user);
		}
	}
	
	private function send403Mail(GDO_User $user) : void
	{
		$url = $this->gdoParameterVar('url');
		$mail = Mail::botMail();
		$mail->setSubject(t('mail_title_403', [sitename(), html($url)]));
		
		$tVars = [
			html($user->renderUserName()),
			sitename(),
			html($url),
			GDO_User::current()->renderName(),
		];
		$mail->setBody(t('mail_body_403', $tVars));
		$mail->sendToUser($user);
	}

}
