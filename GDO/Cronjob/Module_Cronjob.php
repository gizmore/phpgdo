<?php
declare(strict_types=1);
namespace GDO\Cronjob;

use GDO\Core\GDO_Module;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * Cronjob stuff.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
class Module_Cronjob extends GDO_Module
{

	public int $priority = 10;

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Timestamp::make('last_run')->initialAgo(60),
// 			GDT_Percent::make('lottery_chance')->initial('0.5'),
		];
	}

	public function getClasses(): array
	{
		return [
			GDO_Cronjob::class,
		];
	}

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/cronjob');
	}

	##############
	### Module ###
	##############

	public function href_administrate_module(): ?string
	{
		return href('Cronjob', 'Cronjob');
	}

//	public function onModuleInit(): void
//	{
//		# @TODO: Cronjob shall run via any request randomly.
//	}

	public function cfgLastRun(): string
	{
		return $this->getConfigVar('last_run');
	}

	public function setLastRun(): void
	{
		$this->saveConfigVar('last_run', Time::getDate());
	}

}
