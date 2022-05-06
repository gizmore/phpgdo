<?php
namespace GDO\Core;

/**
 * An abstract DB driven field.
 * This phpdoc shall be a reference documentation entry for v7.
 * [This](https://github.com/gizmore/phpgdo) is a link to a website to see bleeding stuff while i try a fresh start. 
 * 
 * - Abstracts gdoColumnDefine() : string
 * 
 * - Attributes bool $primary
 * - Attributes bool $unique
 * - Attributes bool $virtual
 * 
 * - Defaults $searchable, $filterable and $orderable to true.
 * 
 * @author gizmore
 * @see GDT_String
 * @version 7.0.0
 * @since 7.0.0
 */
abstract class GDT_DBField extends GDT_Field
{
	#################
	### GDT_Field ###
	#################
	public bool $readable = true;
	public bool $writable = true;
	
	####################
	### Create Table ###
	####################
	public abstract function gdoColumnDefine() : string;
	
	###############
	### Primary ###
	###############
	public bool $primary = false;
	public function primary(bool $primary = true) : self
	{
		$this->primary = $primary;
		return $this;
	}
	
	##############
	### Unique ###
	##############
	public bool $unique = false;
	public function unique(bool $unique = true) : self
	{
		$this->unique = $unique;
		return $this;
	}
	
	###############
	### Virtual ###
	###############
	public bool $virtual = false;
	public function virtual(bool $virtual = true) : self
	{
		$this->virtual = $virtual;
		return $this;
	}
	
}
