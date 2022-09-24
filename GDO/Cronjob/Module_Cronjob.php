<?php
namespace GDO\Cronjob;

use GDO\Core\GDO_Module;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * Cronjob stuff.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
class Module_Cronjob extends GDO_Module
{
	public int $priority = 10;
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Timestamp::make('last_run')->initialAgo(60),
// 			GDT_Percent::make('lottery_chance')->initial('0.5'),
		];
	}
	
	public function cfgLastRun()
	{
		return $this->getConfigVar('last_run');
	}
	
	public function setLastRun()
	{
		$this->saveConfigVar('last_run', Time::getDate());
	}
	
	##############
	### Module ###
	##############
	public function getClasses() : array
    {
        return [
            GDO_Cronjob::class,
        ];
    }
    
	public function onLoadLanguage() : void
	{
	    $this->loadLanguage('lang/cronjob');
	}

	public function href_administrate_module() : ?string
	{
		return href('Cronjob', 'Cronjob');
	}
	
	public function onModuleInit()
	{
		# @TODO: Cronjob shall run via any request randomly.
	}
	
}
