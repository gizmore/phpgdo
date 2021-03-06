<?php
namespace GDO\Core\Method;

use GDO\Net\GDT_Url;
use GDO\UI\MethodPage;
use GDO\Core\Module_Core;
use GDO\Mail\Mail;
use GDO\User\GDO_User;

/**
 * Render a 404 page.
 * Send mails on this 404 event, if enabled.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class FileNotFound extends MethodPage
{
	public function saveLastUrl() : bool { return false; }
	
	public function getMethodTitle() : string
	{
		return t('file_not_found');
	}
	
	public function getMethodDescription() : string
	{
		return t('file_not_found');
	}
	
	public function gdoParameters() : array
	{
		return [
			GDT_Url::make('url')->notNull()->allowInternal(),
		];
	}
	
	public function beforeExecute() : void
	{
		if (Module_Core::instance()->cfgMail404())
		{
			$this->send404Mails();
		}
	}
	
	public function execute()
	{
		return $this->pageTemplate('404_page');
	}
	
	private function send404Mails() : void
	{
		foreach (GDO_User::staff() as $user)
		{
			$this->send404Mail($user);
		}
	}

	private function send404Mail(GDO_User $user) : void
	{
		$url = $this->gdoParameterVar('url');
		$mail = Mail::botMail();
		$mail->setSubject(t('mail_title_404', [sitename(), html($url)]));

		$tVars = [
			html($user->renderUserName()),
			sitename(),
			html($url),
			GDO_User::current()->renderName(),
		];
		$mail->setBody(t('mail_body_404', $tVars));
		$mail->sendToUser($user);
	}
	
}
