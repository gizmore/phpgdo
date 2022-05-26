<?php
namespace GDO\Language\Method;

use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Redirect;
use GDO\Core\MethodAjax;
use GDO\Language\GDO_Language;

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
			GDT_Url::make('_ref')->allowExternal(false)->allowLocal(),
		];
	}
	
	public function getDescription()
	{
	    if ($this->getLanguage(false))
	    {
	        return t($this->getDescriptionLangKey(), [$this->getLanguage()->renderName()]);
	    }
	    else
	    {
	        return t($this->getDescriptionLangKey().'2');
	    }
	}
	
	/**
	 * @return \GDO\Language\GDO_Language
	 */
	protected function getLanguage(bool $throw=true) : ?GDO_Language
	{
	    try
	    {
	        return $this->gdoParameterValue('lang', true);
	    }
	    catch (\Throwable $ex)
	    {
	        if ($throw)
	        {
	            throw $ex;
	        }
	        return null;
	    }
	}

	/**
	 * Switch the language and redirect back.
	 */
	public function execute()
	{
		# Set new ISO language
		$iso = $this->getLanguage()->getISO();
		$_SERVER['REQUEST_URI'] = preg_replace("/_lang=[a-z]{2}/", "_lang=".$iso , urldecode($_SERVER['REQUEST_URI']));
// 		$_REQUEST['_lang'] = $iso;
		GDO_Session::set('gdo-language', $iso);
		Trans::setISO($iso);
		return GDT_Redirect::make()->
			back($this->gdoParameterVar('_ref'))->
			redirectMessage('msg_language_set', [$this->getLanguage()->renderName()]);
	}
	
}
