<?php
namespace GDO\Form;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Date\GDT_Duration;
use GDO\Date\Time;
use GDO\Core\GDT_Checkbox;
use GDO\Core\Website;

final class Module_Form extends GDO_Module
{
	public int $priority = 10;
	
	public function isCoreModule() : bool
	{
		return true;
	}
	
	public function getFriendencies() : array
	{
		return [
			'Session',
		];
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Checkbox::make('xsrf_header')->initial('1'),
			GDT_EnumNoI18n::make('xsrf_mode')->enumValues('off', 'fixed', 'secure')->initial('secure')->notNull(),
			GDT_Duration::make('xsrf_expire')->min(60)->max(Time::ONE_DAY)->initial('1h')->notNull(),
		];
	}
	public function cfgXSRFHeader() : bool { return $this->getConfigValue('xsrf_header'); }
	public function cfgXSRFMode() : string { return $this->getConfigVar('xsrf_mode'); }
	public function cfgXSRFDuration() : int { return $this->getConfigValue('xsrf_expire'); }
	
	### init 
	public function onModuleInit(): void
	{
		if ($this->cfgXSRFHeader())
		{
			Website::addMeta([
				'csrf-token',
				GDT_AntiCSRF::fixedToken(),
				'name',
			]);
		}
	}
	
}
