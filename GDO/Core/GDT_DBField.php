<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * An abstract DB driven field.
 * This phpdoc shall be a reference documentation entry for v7.
 * [This](https://github.com/gizmore/phpgdo) is a link to a website to see bleeding stuff while i try a fresh start.
 *
 * - Attributes bool $primary
 * - Attributes bool $unique
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see GDO
 * @see GDT_String
 */
abstract class GDT_DBField extends GDT_Field
{

	use WithGDO;

	###############
	### Primary ###
	###############
	public bool $primary = false;


	public function primary(bool $primary = true): static
	{
		$this->primary = $primary;
		return $this->notNull($primary);
	}

	public function isPrimary(): bool
	{
		return $this->primary;
	}


	##############
	### Unique ###
	##############


	public bool $unique = false;


	public function isUnique(): bool
	{
		return $this->unique;
	}

	public function unique(bool $unique = true): static
	{
		$this->unique = $unique;
		return $this;
	}

}
