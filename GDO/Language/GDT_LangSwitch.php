<?php
namespace GDO\Language;

use GDO\Core\GDT_Select;
use GDO\Core\GDT_Template;
use GDO\Util\Strings;

/**
 * Displays a language switcher.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
final class GDT_LangSwitch extends GDT_Select
{
    public function getDefaultName() : string { return '_lang'; }
    
    protected function __construct()
    {
        parent::__construct();
        $this->choices(Module_Language::instance()->cfgSupported());
    }
    
    public function renderHTML() : string
	{
		return GDT_Template::php('Language', 'langswitch_html.php', ['field' => $this]);
	}
	
	public function hrefLangSwitch(GDO_Language $language)
	{
	    $iso = $language->getISO();
	    $q = $_SERVER['QUERY_STRING'];
	    $c = 0;
	    $q = preg_replace('#_lang=[a-z]{2}#', '_lang='.$iso, $q, 1, $c);
	    if ($c == 0)
	    {
	        $q = $q ? ($q.'&_lang=' . $iso) : ('_lang=' .  $iso);
	    }
	    $u = urldecode($_SERVER['REQUEST_URI']);
	    $u = Strings::substrTo($u, '?', $u);
	    return $u . '?' . $q;
	}
	
}
