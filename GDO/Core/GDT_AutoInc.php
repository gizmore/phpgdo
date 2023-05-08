<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Database;

/**
 * The auto inc column is unsigned and sets the primary key after insertions.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 * @see GDT_CreatedAt
 * @see GDT_CreatedBy
 * @see GDT_EditedAt
 * @see GDT_EditedBy
 */
final class GDT_AutoInc extends GDT_UInt
{

	############
	### Base ###
	############
	public bool $notNull = true;
	public bool $writeable = false;

	public function defaultLabel(): self { return $this->label('id'); }

	##############
	### Column ###
	##############
	public function primary(bool $primary = true): static
	{
		return $this;
	}

	public function isPrimary(): bool
	{
		# Weird workaround for mysql primary key defs.
		return true;
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		return true;
	}

	##############
	### Events ###
	##############
	/**
	 * After creation store the auto inc value.
	 *
	 * @throws GDO_ExceptionFatal
	 */
	public function gdoAfterCreate(GDO $gdo): void
	{
		$id = Database::instance()->insertId();
		$gdo->setVar($this->name, (string)$id, false);
	}

// 	public function blankData() : array
// 	{
// 		# prevent old values to be used.
// // 		return GDT::EMPTY_ARRAY;
// 		return [$this->name => null];
// 	}

	/**
	 * Better not plug anything,
	 * as it would cause to force null instead of ignoring the auto inc field.
	 * We have to override the '4' from GDT_UInt.
	 */
	public function plugVars(): array
	{
		return GDT::EMPTY_ARRAY;
	}

}
