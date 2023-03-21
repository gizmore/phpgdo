<?php
namespace GDO\Cronjob;

use GDO\Core\Logger;
use GDO\Core\Method;

/**
 * Baseclass method for a cronjob.
 * Override runAt() to setup intervals with crontab syntax.
 * Module_Cronjob takes care of interval computations.
 *
 * @version 6.11.4
 * @since 6.1.0
 * @author gizmore
 */
abstract class MethodCronjob extends Method
{

	public function getMethodTitle(): string
	{
		return t('cronjob_method', [$this->getModuleName(), $this->getMethodName()]);
	}

	public function getPermission(): ?string { return 'cronjob'; }

	public function execute()
	{
		$this->start();
		$this->run();
		$this->end();
	}

	public function start() { Logger::logCron('[START] ' . get_called_class()); }

	abstract public function run();

	public function end() { Logger::logCron('[DONE] ' . get_called_class()); }

	/**
	 * Override runAt() to set interval via crontab runat syntax.
	 *
	 * @return string
	 */
	public function runAt()
	{
		return '* * * * *';
	}

	public function log($msg)
	{
		Logger::logCron('[+] ' . $msg);
	}

	###########
	### Log ###
	###########

	public function logError($msg)
	{
		Logger::logCron('[ERROR] ' . $msg);
	}

	public function logWarning($msg)
	{
		Logger::logCron('[WARNING] ' . $msg);
	}

	public function logNotice($msg)
	{
		Logger::logCron('[NOTICE] ' . $msg);
	}

	protected function runHourly()
	{
		return '0 * * * *';
	}

	protected function runDaily($hour)
	{
		return $this->runDailyAt(0);
	}

	protected function runDailyAt($hour): string
	{
		return "0 $hour * * *";
	}

}
