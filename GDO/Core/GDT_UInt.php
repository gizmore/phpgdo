<?php
namespace GDO\Core;

/**
 * Unsigned version of GDT_Int
 * Sets a min value of 0.
 * Sets unsigned.
 * Sets default order direction to descending.
 * Base class of GDT_Object.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.5.0
 * 
 * @see GDT_Object
 */
class GDT_UInt extends GDT_Int
{
    public ?float $min = 0;
    public bool $unsigned = true;
    
    public function isDefaultAsc() : bool
    {
    	return false;
    }
    
    public function configJSON() : array
    {
    	return array_merge(parent::configJSON(), [
    		'signed' => false,
    	]);
    }
    
}
