<?php
namespace GDO\Language\Method;

use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\UI\GDT_Redirect;
use GDO\Core\MethodAjax;
use GDO\Language\GDO_Language;
use GDO\User\GDO_User;
use GDO\Net\GDT_Url;

/**
 * Switch language to user's choice.
 * Overrides HTTP_ACCEPT_LANGUAGE and user_lang for language detection in Module_Language.
 * Stores your choice in your session.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.9.0
 * 
 * @see Module_Language
 * @see GDO_Session
 */
final class SwitchLanguage extends MethodAjax
{
	public function saveLastUrl() : bool
	{
		return false;
	}
	
	public function gdoParameters() : array
	{
		return [
			GDT_Language::make('lang')->notNull(),
			GDT_Url::make('_ref')->allowInternal(),
		];
	}
	
	public function getMethodDescription() : string
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
	
	/**
	 * @return \GDO\Language\GDO_Language
	 */
	protected function getLanguage(bool $throw=true) : ?GDO_Language
	{
        return $this->gdoParameterValue('lang', true, $throw);
	}

	/**
	 * Switch the language and redirect back.
	 */
	public function execute()
	{
		# Set new ISO language
		$iso = $this->getLanguage()->getISO();
		if (!($ref = $this->gdoParameterVar('_ref')))
		{
			$ref = GDT_Redirect::hrefBack();
		}
		$ref = preg_replace("/_lang=[a-z]{2}/", "_lang=".$iso , $ref);
		GDO_User::current()->persistent()->saveVar('user_language', $iso);
		Trans::setISO($iso);
		return GDT_Redirect::make()->
			href($ref)->
			redirectMessage('msg_language_set', [$this->getLanguage()->renderName()]);
			
	}
	
}
