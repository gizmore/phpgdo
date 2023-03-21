<?php
namespace GDO\Language\Method;

use GDO\Core\MethodAjax;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;

/**
 * Switch language to user's choice.
 * Overrides HTTP_ACCEPT_LANGUAGE and user_lang for language detection in Module_Language.
 * Stores your choice in your session.
 *
 * @version 7.0.1
 * @since 6.9.0
 * @author gizmore
 * @see GDO_Language
 * @see Module_Language
 */
final class SwitchLanguage extends MethodAjax
{

	public function gdoParameters(): array
	{
		return [
			GDT_Language::make('lang')->notNull(),
			GDT_Url::make('_ref')->allowInternal(),
		];
	}

	public function getMethodTitle(): string
	{
		return t('language');
	}

	public function getMethodDescription(): string
	{
		if ($this->getLanguage(false))
		{
			return t('md_switch_language', [$this->getLanguage()->renderName()]);
		}
		else
		{
			return t('md_switch_language2');
		}
	}

	protected function getLanguage(bool $throw = true): ?GDO_Language
	{
		return $this->gdoParameterValue('lang', $throw, $throw);
	}

	/**
	 * Switch the language and redirect back.
	 */
	public function execute()
	{
		# Set new ISO language
		$lang = $this->getLanguage();
		$iso = $lang->getISO();
		Trans::setISO($iso);

		# Redirect back to new language
		if (!($ref = $this->gdoParameterVar('_ref')))
		{
			$ref = GDT_Redirect::hrefBack();
		}
		$ref = GDT_Link::replacedHREFS($ref, '_lang', $iso);

		# Save new iso
		$user = GDO_User::current()->persistent();
		$user->saveSettingVar('Language', 'language', $iso);

		# Do it
		return GDT_Redirect::make()->
		href($ref)->
		redirectMessage('msg_language_set', [$lang->renderName()]);
	}

}
