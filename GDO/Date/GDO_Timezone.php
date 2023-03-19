<?php
namespace GDO\Date;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Int;
use GDO\Core\GDT_Index;

/**
 * Timezone mapping entities.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.7
 */
final class GDO_Timezone extends GDO
{
	public function isTestable() : bool { return false; }
	
	public function gdoCached() : bool { return false; }
	
	###############
	### Factory ###
	###############
	/**
	 * Get a timezone by name.
	 * @example Europe/Berlin
	 */
	public static function getByName($name): static
	{
		return self::getBy('tz_name', $name);
	}
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('tz_id')->bytes(2),
			GDT_Name::make('tz_name')->notNull(),
			GDT_Int::make('tz_offset')->bytes(2)->notNull()->initial('0'),
//			GDT_Index::make('tz_index_name')->indexColumns('tz_name')->btree(),
		];
	}
	
	###############
	### Getters ###
	###############
	public function getName() : ?string { return $this->gdoVar('tz_name'); }
	public function getOffset() { return $this->gdoVar('tz_offset'); }

	###############
	### Display ###
	###############
	public function renderName() : string
	{
		return $this->getName() . ' ' . $this->displayOffset();
	}
	
	public function renderOption() : string
	{
		if ($name = $this->getName())
		{
			return $name;
		}
		return '';
	}
	
	public function displayOffset()
	{
		$o = $this->getOffset();
		$oo = abs($o);
		return sprintf('%s%02d%02d',
			$o >= 0 ? '+' : '-',
			$oo / 60, $oo % 60
		);
	}
	
	#######################
	### Timezone Object ###
	#######################
	/**
	 * @return \DateTimeZone
	 */
	public function getTimezone()
	{
		return Time::getTimezoneObject($this->getID());
	}

	#############
	### Cache ###
	#############
	public function allTimezones()
	{
		return array_values($this->allCached('tz_name', true));
	}
	
}
