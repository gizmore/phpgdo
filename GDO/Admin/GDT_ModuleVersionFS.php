<?php
namespace GDO\Admin;

use GDO\Core\GDO;
use GDO\Core\GDT_Version;

/**
 * The filesystem version of a module.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.0
 */
final class GDT_ModuleVersionFS extends GDT_Version
{
	public function gdo(GDO $gdo=null) : self
	{
	    $this->var = $gdo->version;
	    return $this;
	}
	
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		$va = $a->version;
		$vb = $b->version;
		return $va === $vb ? 0 : ($va < $vb ? -1 : 1);
	}
	
	public function getVar() : ?string
	{
	    $m = $this->getModule();
	    return $m ? $m->version : '0';
	}

}
