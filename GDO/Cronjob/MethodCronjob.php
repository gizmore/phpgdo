<?php
declare(strict_types=1);
namespace GDO\Cronjob;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\User\GDO_Permission;

/**
 * Baseclass method for a cronjob.
 * Override runAt() to setup intervals with crontab syntax.
 * Module_Cronjob takes care of interval computations.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
abstract class MethodCronjob extends Method
{

	abstract public function run(): void;



	public function getMethodTitle(): string
	{
		return t('cronjob_method', [$this->getModuleName(), $this->getMethodName()]);
	}

	public function getPermission(): ?string
	{
		return GDO_Permission::CRONJOB;
	}

	public function execute(): GDT
	{
		$this->start();
		$this->run();
		$this->end();
		return GDT_Response::make();
	}

	public function start(): void
	{
		Logger::logCron('[START] ' . get_called_class());
	}

	public function end(): void
	{
		Logger::logCron('[DONE] ' . get_called_class());
	}

	/**
	 * Override runAt() to set interval via crontab runat syntax.
	 */
	public function runAt(): string
	{
		return '* * * * *';
	}

	public function log($msg): void
	{
		Logger::logCron('[+] ' . $msg);
	}


	###########
	### Log ###
	###########


	public function logError($msg): void
	{
		Logger::logCron('[ERROR] ' . $msg);
	}

	public function logWarning($msg): void
	{
		Logger::logCron('[WARNING] ' . $msg);
	}

	public function logNotice($msg): void
	{
		Logger::logCron('[NOTICE] ' . $msg);
	}


	##################
	### Cron Hours ###
	##################


	protected function runHourly(): string
	{
		return '0 * * * *';
	}

	protected function runDaily(int $hour=0): string
	{
		return $this->runDailyAt(0);
	}

	protected function runDailyAt(int $hour): string
	{
		return "0 {$hour} * * *";
	}

}
