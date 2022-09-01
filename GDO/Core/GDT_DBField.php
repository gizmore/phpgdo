<?php
namespace GDO\Core;

/**
 * An abstract DB driven field.
 * This phpdoc shall be a reference documentation entry for v7.
 * [This](https://github.com/gizmore/phpgdo) is a link to a website to see bleeding stuff while i try a fresh start. 
 * 
 * - Abstracts gdoColumnDefine() : string (now GDT)
 * 
 * - Attributes bool $primary
 * - Attributes bool $unique
 * 
 * - Defaults $searchable, $filterable and $orderable to true.
 * 
 * @author gizmore
 * @see GDT_String
 * @version 7.0.1
 * @since 7.0.0
 */
abstract class GDT_DBField extends GDT_Field
{
	####################
	### Create Table ###
	####################
	public function gdoInitialDefine() : string
	{
		return isset($this->initial) ?
			(' DEFAULT '.GDO::quoteS($this->initial)) : '';
	}
	
	###############
	### Primary ###
	###############
	public bool $primary = false;
	public function primary(bool $primary = true) : self
	{
		$this->primary = $primary;
		return $this;
	}
	
	public function isPrimary() : bool
	{
		return $this->primary;
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
	
	public function isUnique() : bool
	{
		return $this->unique;
	}
	
	##################
	### Identifier ###
	##################
	public function identifier()
	{
		return $this->name;
	}
	
}
