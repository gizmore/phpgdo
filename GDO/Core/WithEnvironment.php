<?php
namespace GDO\Core;

use GDO\User\GDO_User;

/**
 * WithEnvironment is for Method execution.
 * GDT_Method uses it to keep track of the execution state.
 * 
 * Add GDT result attribute.
 * Add effective GDO_User attribute.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithEnvironment
{
	use WithFields;
	
	##############
	### Run As ###
	##############
	public GDO_User $runAs;
	/**
	 * Optional user for running the method. Default is GDO_User::current()
	 * 
	 * @param GDO_User $runAs
	 * @return self
	 */
	public function runAs(GDO_User $runAs=null) : self
	{
		$this->runAs = $runAs ? $runAs : GDO_User::current();
		return $this;
	}
	
	##############
	### Method ###
	##############
	public Method $method;
	public function method(Method $method) : self
	{
		$this->method = $method;
		return $this;
	}
	
	##############
	### Result ###
	##############
	public GDT $result;
	public function result(GDT $result) : self
	{
		$this->result = $result;
		return $this;
	}
	
	###############
	### Execute ###
	###############
	protected function changeUser(bool $changeLocale=false) : self
	{
		$user = isset($this->runAs) ? $this->runAs : GDO_User::current();
		GDO_User::setCurrent($user, $changeLocale);
		return $this;
	}
	
}
