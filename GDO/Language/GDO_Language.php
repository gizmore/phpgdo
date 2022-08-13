<?php
namespace GDO\Language;

use GDO\Core\GDO;
use GDO\Core\GDT_Char;
use GDO\Core\GDT_Template;

/**
 * Language table.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.1
 */
final class GDO_Language extends GDO
{
// 	public static function iso() { return Trans::$ISO; }
	
// 	/**
// 	 * Wrap a callback in a temporary changed ISO. Esthetics.
// 	 * @param string $iso
// 	 * @param callable $callback
// 	 * @return mixed
// 	 */
// 	public static function withIso($iso, $callback)
// 	{
// 		$old = self::iso();
// 	    try 
// 	    {
//     		Trans::setISO($iso);
//     		return call_user_func($callback);
// 	    }
// 	    catch (\Throwable $t)
// 	    {
// 	        throw $t;
// 	    }
// 	    finally
// 	    {
//     		Trans::setISO($old);
// 	    }
// 	}

	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
		    GDT_Char::make('lang_iso')->notNull()->primary()->ascii()->caseS()->length(2),
		];
	}
	public function getID() : ?string { return $this->gdoVar('lang_iso'); }
	public function getISO() : string { return $this->getID(); }
	
	##############
	### Render ###
	##############
	public function renderName() : string
	{
		return $this->isValid() ? 
			t('lang_'.$this->getISO()) : 
			t('language');
	}
	
	public function renderNameISO(string $iso) : string
	{
		return $this->isValid() ?
			tiso($iso, 'lang_'.$this->getISO()) :
			t('language');
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Language', 'language_html.php', ['language' => $this]);
	}
	
	public function renderOption() : string
	{
		return GDT_Template::php('Language', 'language_option.php', ['language' => $this]);
	}
	
	##############
	### Static ###
	##############
	/**
	 * Get a language by ISO or return a stub object with name "Unknown".
	 */
	public static function getByISOOrUnknown(string $iso=null) : self
	{
		if ( ($iso === null) || (!($language = self::getById($iso))) )
		{
			$language = self::blank(['lang_iso'=>'zz']);
		}
		return $language;
	}
	
	public static function current() : self
	{
		return self::getByISOOrUnknown(Trans::$ISO);
	}
	
	/**
	 * @return self[]
	 */
	public function allSupported() : array
	{
		return Module_Language::instance()->cfgSupported();
	}
	
// 	public static function getMainLanguage() : self
// 	{
// 		return self::findById(GDO_LANGUAGE);
// 	}
	
}
