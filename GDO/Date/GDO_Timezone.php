<?php
declare(strict_types=1);
namespace GDO\Date;

use DateTimeZone;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Int;
use GDO\Core\GDT_Name;

/**
 * Timezone mapping entities.
 *
 * @version 7.0.3
 * @since 6.10.7
 * @author gizmore
 */
final class GDO_Timezone extends GDO
{

	/**
	 * Get a timezone by name.
	 *
	 * @example Europe/Berlin
	 */
	public static function getByName($name): self
	{
		return self::getBy('tz_name', $name);
	}

	public function isTestable(): bool { return false; }

	###############
	### Factory ###
	###############

//	public function gdoCached(): bool { return false; }

	###########
	### GDO ###
	###########

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('tz_id')->bytes(2),
			GDT_Name::make('tz_name')->notNull(),
			GDT_Int::make('tz_offset')->bytes(2)->notNull()->initial('0'),
		];
	}

	###############
	### Getters ###
	###############

	public function getTimezone(): DateTimeZone
	{
		return Time::getTimezoneObject($this->getID());
	}

	/**
	 * @return self[]
	 */
	public function allTimezones(): array
	{
		return array_values($this->allCached('tz_name', true));
	}

	public function getName(): ?string { return $this->gdoVar('tz_name'); }


	public function getOffset(): int { return $this->gdoValue('tz_offset'); }

	###############
	### Display ###
	###############
	public function renderName(): string
	{
		return $this->getName() . ' ' . $this->displayOffset();
	}

	public function renderOption(): string
	{
		if ($name = $this->getName())
		{
			return $name;
		}
		return GDT::EMPTY_STRING;
	}

	public function displayOffset(): string
	{
		$o = $this->getOffset();
		$oo = abs($o);
		return sprintf('%s%02d%02d',
			$o >= 0 ? '+' : '-',
			$oo / 60, $oo % 60
		);
	}


}
