<?php
namespace GDO\Core;

use GDO\Table\Module_Table;
use GDO\UI\GDT_SearchField;

/**
 * Generic autocompletion base code.
 * Override 1 method for full implemented completion of a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.0
 * @see GDT_Table
 */
abstract class MethodCompletion extends MethodAjax
{
    public function gdoParameters() : array
    {
        return [
            GDT_SearchField::make('query')->notNull(),
        ];
    }
    
    #############
    ### Input ###
    #############
	public function getSearchTerm() : string
	{
		if ($var = $this->gdoParameterVar('query'))
		{
			return $var;
		}
		return '';
	}
	
	public function getMaxSuggestions() : int
	{
		return Module_Table::instance()->cfgSuggestionsPerRequest();
	}
	
}
