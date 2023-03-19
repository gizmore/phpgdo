<?php
namespace GDO\Language;

use GDO\Core\GDO;
use GDO\Core\GDT_Char;
use GDO\Core\GDT_Template;

/**
 * Language table.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.1
 */
final class GDO_Language extends GDO
{

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
	
	public function hrefFlag() : string
	{
		return Module_Language::instance()->wwwPath("img/{$this->getISO()}");
	}
	
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
	public static function getByISOOrUnknown(string $iso=null): static
	{
		if ( ($iso === null) || (!($language = self::getById($iso))) )
		{
			$language = self::blank(['lang_iso'=>'zz']);
		}
		return $language;
	}
	
	public static function current(): static
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
	
	/**
	 * Get all language isos that are officially supported by phpgdo.
	 * @return string[]
	 */
	public static function gdoSupported(): array
	{
		return ['en', 'de', 'it', 'fr'];
	}
	
	public static function bestSupported(string $prefer, string $default=GDO_LANGUAGE): string
	{
		$all = self::gdoSupported();
		return in_array($prefer, $all) ? $prefer : $default;
	}
	
}
