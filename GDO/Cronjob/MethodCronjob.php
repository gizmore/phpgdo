<?php
namespace GDO\Cronjob;

use GDO\Core\Logger;
use GDO\Core\Method;

/**
 * Baseclass method for a cronjob.
 * Override runAt() to setup intervals with crontab syntax.
 * Module_Cronjob takes care of interval computations.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.1.0
 */
abstract class MethodCronjob extends Method
{
	public abstract function run();

	public function getPermission() : ?string { return 'cronjob'; }

	/**
	 * Override runAt() to set interval via crontab runat syntax.
	 * @return string
	 */
	public function runAt()
	{
		return "* * * * *";
	}
	
	protected function runHourly()
	{
		return "0 * * * *";
	}
	
	protected function runDaily($hour)
	{
		return $this->runDailyAt(0);
	}
	
	protected function runDailyAt($hour)
	{
		return "0 $hour * * *";
	}
	
	public function execute()
	{
		$this->start();
		$this->run();
		$this->end();
	}

	###########
	### Log ###
	###########
	public function start() { Logger::logCron('[START] '.get_called_class()); }
	public function end() { Logger::logCron('[DONE] '.get_called_class()); }

	public function log($msg) { Logger::logCron('[+] '.$msg); }
	public function logError($msg) { Logger::logCron('[ERROR] '.$msg); return false; }
	public function logWarning($msg) { Logger::logCron('[WARNING] '.$msg); }
	public function logNotice($msg) { Logger::logCron('[NOTICE] '.$msg); }

}
