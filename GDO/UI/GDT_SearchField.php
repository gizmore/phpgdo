<?php
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * A search field is a text with icon and default label.
 * Input type is set to search.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
class GDT_SearchField extends GDT_String
{
	public $icon = 'search';
	
	public int $min = 3;
	public int $max = 128;
	
	public bool $hidden = true;
    
    public function defaultName() { return 'search'; }
	public function defaultLabel() : self { return $this->label('search'); }
	public function getInputType() : string { return 'search'; }
	
    public function isSerializable() : bool { return false; }
    public function isOrderable() : bool { return false; }
    public function isSearchable() : bool { return false; }
    public function isFilterable() : bool { return false; }

	public function gdoExampleVars()
	{
	    return t('search_term');
	}
	
	public function plugVar() : string
	{
		return 'ess';
	}

}
