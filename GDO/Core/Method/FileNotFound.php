<?php
namespace GDO\Core\Method;

use GDO\Core\GDO_ArgError;
use GDO\Core\GDT;
use GDO\Core\Module_Core;
use GDO\Mail\Mail;
use GDO\Net\GDT_Url;
use GDO\UI\MethodPage;
use GDO\User\GDO_User;

/**
 * Render a 404 page.
 * Send mails on this 404 event, if enabled.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class FileNotFound extends MethodPage
{

	public function isHiddenMethod(): bool
	{
		return true;
	}

	public function isSavingLastUrl(): bool { return false; }

	protected function isFileCacheEnabled(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('file_not_found');
	}

	public function getMethodDescription(): string
	{
		return t('file_not_found');
	}

	public function getTemplateName(): string
	{
		return 'page/404_page.php';
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Url::make('_url')->notNull()->allowInternal(),
		];
	}

    public function execute(): GDT
    {
        hdrc('HTTP/1.1 404 File not found');
        return parent::execute();
    }

	############
	### Mail ###
	############
    /**
     * @throws GDO_ArgError
     */
    public function beforeExecute(): void
	{
		if (Module_Core::instance()->cfgMail404())
		{
			if (module_enabled('Mail'))
			{
				$this->send404Mail(GDO_ERROR_EMAIL);
			}
		}
	}

//	private function send404Mails(): void
//	{
//		$url = $this->gdoParameterVar('url');
//		if (!str_ends_with($url, '.map'))
//		{
//
//			foreach (GDO_User::admins() as $user)
//			{
//				$this->send404Mail($user);
//			}
//		}
//	}

    /**
     * @throws GDO_ArgError
     */
    private function send404Mail(string $recipient): void
	{
		$url = $this->gdoParameterVar('_url');
		$mail = Mail::botMail();
		$mail->setSubject(t('mail_title_404', [sitename(), html($url)]));
		$args = [
			t('staff'),
			sitename(),
			html($url),
			GDO_User::current()->renderName(),
			html(@$_SERVER['HTTP_REFERER']),
		];
		$mail->setBody(t('mail_body_404', $args));
        $mail->setReceiver($recipient);
		$mail->sendAsText();
	}

}
