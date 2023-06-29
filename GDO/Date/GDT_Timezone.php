<?php
declare(strict_types=1);
namespace GDO\Date;

use GDO\Core\GDO_DBException;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_ObjectSelect;

/**
 * Timezone select.
 * inputToVar() does convert +NNNN to the first timezone matching the offset.
 * Likes an autocompletion provider.
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class GDT_Timezone extends GDT_ObjectSelect
{

	protected function __construct()
	{
		parent::__construct();
		$this->notNull();
		$this->table(GDO_Timezone::table());
		$this->initial('1'); # UTC
		$this->icon('time');
		$this->completionHref(href('Date', 'TimezoneComplete'));
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'gdo_timezone';
	}

	public function gdtDefaultName(): ?string
	{
		return 'timezone';
	}

	public function isSearchable(): bool
	{
		return false;
	}

	public function inputToVar(array|string|null|GDT_Method $input): ?string
	{
		if ($input !== null)
		{
			# If this was no autocompletion, try numeric parsing of offset.
			# The timezone name could be checked in parent (via GDT_Name autocompletion).
			if ($this->wasNoCompletion())
			{
				$input = trim($input);
				if (preg_match('#^[\\-+]?\\d{3,4}$#D', $input))
				{
					$input = $this->getBestTimezoneIdForOffset($input);
				}
			}
		}
		return $input;
	}

	/**
	 * Check if the input was generated by auto completion, so it is an ID.
	 */
	protected function wasNoCompletion(): bool
	{
		$key = "nocompletion_{$this->name}";
		return !!@$this->inputs[$key];
	}

	# ##############
	# ## Private ###
	# ##############

	/**
	 * Get a timezone ID matching our offset.
	 * Not perfect.
	 *
	 * @throws GDO_DBException
	 */
	private function getBestTimezoneIdForOffset(int $offset): string
	{
		return GDO_Timezone::table()->select("tz_id, ABS(tz_offset-{$offset}) tzd")
			->order('tzd ASC, rand()')
			->first()
			->exec()
			->fetchVar();
	}

	/**
	 * In unit tests, try UTC and Berlin.
	 */
	public function plugVars(): array
	{
		$name = $this->getName();
		return [
			[
				$name => GDO_Timezone::getBy('tz_name', 'UTC')->getID(),
			],
			[
				$name => GDO_Timezone::getBy('tz_name', 'Europe/Berlin')->getID(),
			],
		];
	}

}
