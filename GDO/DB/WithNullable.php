<?php
namespace GDO\DB;

/**
 * Add nullable option to a GDT.
 *
 * @author gizmore
 * @version 7.0.2
 */
trait WithNullable
{

	# ###############
	# ## Not null ###
	# ###############
	public bool $notNull = false;

	/**
	 * Change nullable setting.
	 */
	public function notNull(bool $notNull = true): self
	{
		$this->notNull = $notNull;
		return $this;
	}

	# ###############
	# ## Validate ###
	# ###############
	public function validateNull($value): bool
	{
		return $this->notNull ? ($value === null ? $this->errorNull() : true) : true;
	}

	protected function errorNull(): bool
	{
		return $this->error('err_null_not_allowed');
	}

}
