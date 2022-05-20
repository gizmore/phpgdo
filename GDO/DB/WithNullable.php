<?php
namespace GDO\DB;

/**
 * Add nullable option for GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 */
trait WithNullable
{
	################
	### Not null ###
	################
	public bool $notNull = false;
	
	/**
	 * Change nullable setting.
	 * 
	 * @param bool $notNull
	 * @return self
	 */
	public function notNull(bool $notNull = true) : self
	{
		$this->notNull = $notNull;
		return $this;
	}
	
	##########
	### DB ###
	##########
	public function gdoNullDefine() : string
	{
		return $this->notNull ? ' NOT NULL' : ' NULL';
	}
	
	################
	### Validate ###
	################
	public function validateNull($value) : bool
	{
		return $this->notNull ?
			($value === null ? $this->errorNull() : true) :
			true;
	}

	protected function errorNull() : bool
	{
		return $this->error('err_null_not_allowed');
	}
	
}
