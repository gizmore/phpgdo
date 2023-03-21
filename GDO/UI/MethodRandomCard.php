<?php
namespace GDO\UI;

use GDO\Core\GDO;
use GDO\Core\GDT_DeletedAt;
use GDO\Util\Random;

/**
 * View a single random item from a gdo table as card.
 *
 * @author gizmore
 */
abstract class MethodRandomCard extends MethodCard
{

	public function gdoParameters(): array
	{
		return [];
	}

	public function getObject(): ?GDO
	{
		$table = $this->gdoTable();
		$id = $table->gdoPrimaryKeyColumn()->name;
		$query = $table->select('MAX(' . $id . ')');
		if ($delete = $table->gdoColumnOf(GDT_DeletedAt::class))
		{
			$query->where($delete->name . ' IS NULL');
		}
		$max = $query->exec()->fetchValue();
		if (!$max)
		{
			return null;
		}
		$max = Random::mrand(1, $max);
		return $table->findWhere($id . ' >= ' . $max);
	}

}
