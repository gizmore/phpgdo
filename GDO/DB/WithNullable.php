<?php
declare(strict_types=1);
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * Add nullable option to a GDT.
 *
 * @version 7.0.3
 * @author gizmore
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
	public function notNull(bool $notNull = true): static
	{
		$this->notNull = $notNull;
		return $this;
	}

	# ###############
	# ## Validate ###
	# ###############
	public function validateNull(bool|string|null|int|float|array|object $value): bool
	{
		return $this->notNull ? ($value === null ? $this->errorNull() : true) : true;
	}

	protected function errorNull(): bool
	{
		return $this->error('err_null_not_allowed');
	}

}
