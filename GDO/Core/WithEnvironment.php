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
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
trait WithEnvironment
{

	use WithFields;

	##############
	### Run As ###
	##############
	public GDO_User $runAs;
	public Method $method;

	##############
	### Method ###
	##############
	public GDT $result;

	/**
	 * Optional user for running the method. Default is GDO_User::current()
	 */
	public function runAs(GDO_User $runAs = null): self
	{
		$this->runAs = $runAs ? $runAs : GDO_User::current();
		return $this;
	}

	##############
	### Result ###
	##############

	public function method(Method $method): self
	{
		$this->method = $method;
		return $this;
	}

	public function result(GDT $result): self
	{
		$this->result = $result;
		return $this;
	}

	###############
	### Execute ###
	###############
	protected function changeUser(): self
	{
		$user = isset($this->runAs) ? $this->runAs : GDO_User::current();
		GDO_User::setCurrent($user);
		return $this;
	}

}
