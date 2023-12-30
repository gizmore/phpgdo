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
        $f = GDT_Filter::make();
        $result->filterResult($items, $this->getHeaders(), $f);
        foreach ($items as $gdo)
        {
            foreach ($this->getHeaders() as $gdt)
            {

            }
        }
    }



}