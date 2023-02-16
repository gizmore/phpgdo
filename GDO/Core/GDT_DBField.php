<?php
namespace GDO\Core;

/**
 * An abstract DB driven field.
 * This phpdoc shall be a reference documentation entry for v7.
 * [This](https://github.com/gizmore/phpgdo) is a link to a website to see bleeding stuff while i try a fresh start. 
 * 
 * - Attributes bool $primary
 * - Attributes bool $unique
 * 
 * @author gizmore
 * @see GDT_String
 * @version 7.0.2
 * @since 7.0.0
 */
abstract class GDT_DBField extends GDT_Field
{
	###############
	### Primary ###
	###############
	public bool $primary = false;
	public function primary(bool $primary = true) : self
	{
		$this->primary = $primary;
		$this->notNull($primary);
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
	public function unique(bool $unique = true): self
	{
		$this->unique = $unique;
		return $this;
	}
	
	public function isUnique(): bool
	{
		return $this->unique;
	}
	
	##################
	### Identifier ###
	##################
	public function identifier(): string
	{
		return $this->name;
	}
	
}
