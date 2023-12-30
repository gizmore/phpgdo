<?php
namespace GDO\Core;

use GDO\DB\ArrayResult;
use GDO\Table\GDT_Filter;

abstract class MethodCompletionArray extends MethodCompletion
{
    protected abstract function gdoTable(): GDO;

    protected abstract function getItems(): array;

    protected function getHeaders(): array
    {
        return $this->gdoTable()->gdoColumnsCache();
    }

    public function execute(): GDT
    {
        $items = $this->getItems();
        $result = new ArrayResult($items, $this->gdoTable());
        $result = $result->searchResult($result->getData(), $this->gdoTable(), $this->getHeaders(), $this->getSearchTerm());
        $json = $this->itemsToJSON($result->getData());
        return GDT_Array::make()->value($json);
    }



}