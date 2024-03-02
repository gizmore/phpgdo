<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Table\Module_Table;
use GDO\UI\GDT_SearchField;

/**
 * Generic autocompletion base code.
 * Override 1 method (itemToCompletionJSON) for full implemented completion of a GDO.
 *
 * @version 7.0.1
 * @since 6.3.0
 * @author gizmore
 * @see GDT_Table
 * @see MethodAjax
 */
abstract class MethodCompletion extends MethodAjax
{

	public function gdoParameters(): array
	{
		$min = $this->getSearchTermMinLength();
		return [
			GDT_SearchField::make('query')->notNull()->min($min)->max(228),
		];
	}

	protected function getSearchTermMinLength(): int
	{
		return 2;
	}

	public function execute(): GDT
	{
		$query = $this->buildQuery();
		$result = $query->exec();
		$items = $this->collectItems($result);
		$json = $this->itemsToJSON($items);
		return GDT_Array::make()->value($json);
	}

	protected function buildQuery(): Query
	{
		$max = $this->getMaxSuggestions();
		$term = $this->getSearchTerm();
		$table = $this->gdoTable();
		$query = $this->getQuery();
		$eterm = GDO::escapeSearchS($term);
		foreach ($this->gdoHeaderFields() as $name => $gdt)
		{
			if (!$gdt->isVirtual())
			{
// 				if ($name = $gdt->getName())
// 				{
                $query->orWhere("{$name} COLLATE 'utf8mb4_general_ci' LIKE '%{$eterm}%'");
//				$query->orWhere("{$name} LIKE '%{$eterm}%'");
// 				}
			}
		}
		if ($order = $table->getDefaultOrder())
		{
			$query->order($order);
		}
		return $query->limit($max);
	}

	#############
	### Input ###
	#############

	public function getMaxSuggestions(): int
	{
		return Module_Table::instance()->cfgSuggestionsPerRequest();
	}

	public function getSearchTerm(): string
	{
		if (null !== ($var = $this->gdoParameterVar('query')))
		{
			return $var;
		}
		return GDT::EMPTY_STRING;
	}

	abstract protected function gdoTable(): GDO;

	############
	### Exec ###
	############

	protected function getQuery(): Query
	{
		return $this->gdoTable()->select();
	}

	protected function gdoHeaderFields(): array
	{
		return $this->gdoTable()->gdoColumnsCache();
	}

	protected function collectItems(Result $result): array
	{
		$term = $this->getSearchTerm();
		$q = mb_strtolower($term);
		$items = [];
		while ($gdo = $result->fetchObject())
		{
			# ID match == 1st item
			$id = mb_strtolower($gdo->getID());
			if ($id === $q)
			{
				array_unshift($items, $gdo);
			}
			else
			{
				# append
				$items[] = $gdo;
			}
		}
		return $items;
	}

	protected function itemsToJSON(array $items): array
	{
		return array_map([$this, 'itemToCompletionJSON'], $items);
	}

	public function itemToCompletionJSON(GDO $item): array
	{
		return [
			'id' => $item->getID(),
			'text' => $item->renderName(),
			'display' => $item->renderOption(),
		];
	}

}
