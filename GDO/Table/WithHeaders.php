<?php
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\ArrayResult;
use GDO\Util\Strings;
use GDO\Core\GDT_Fields;

/**
 * - A trait for tables and lists which adds an extra headers variable. This has to be a \GDO\Core\GDT_Fields.
 * - Implements @\GDO\Core\ArrayResult multisort for use in @\GDO\Table\MethodTable.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 */
trait WithHeaders
{
	##############
	### Fields ###
	##############
	public GDT_Fields $headers;
	
	public function getHeaders() : GDT_Fields
	{
		if (!isset($this->headers))
		{
			$this->headers = GDT_Fields::make("{$this->name}_headers");
		}
		return $this->headers;
	}
	
	/**
	 * @return GDT[]
	 */
	public function getHeaderFields() : array
	{
		return $this->getHeaders()->getAllFields();
	}
	
	/**
	 * @param string $name
	 * @return \GDO\Core\GDT
	 */
	public function getHeaderField(string $name) : GDT
	{
		return $this->getHeaders()->getField($name);
	}
	
	public function addHeaderField(GDT $gdt) : self
	{
		$this->getHeaders()->addField($gdt);
		return $this;
	}
	
	public function addHeaderFields(GDT...$gdt) : self
	{
		$this->getHeaders()->addFields(...$gdt);
		return $this;
	}
	
	###############
	### Ordered ###
	###############
	public function sortArray(array &$data, string $orders) : array
	{
		$this->result(new ArrayResult($data, $this->gdo));
		$this->multisort($orders);
		return $this->getResult()->getData();
	}
	
	/**
	 * PHP Sorting is unstable.
	 * This method does a stable multisort on an ArrayResult.
	 * @param ArrayResult $result
	 * @return ArrayResult
	 */
	public function multisort(string $defaultOrder=null) : ArrayResult
	{
		$result = $this->getResult();
		$orders = $this->getOrders($defaultOrder);
		$sort_func = $this->make_cmp($orders);
		uasort($result->getData(), $sort_func);
		return $result;
	}
	
	/**
	 * Build the order array from an order string.
	 * @return bool[string]
	 */
	private function getOrders(string $defaultOrder=null) : array
	{
		$orders = [];
		$this->getHeaders()->inputs($this->getInputs());
		foreach ($this->getHeaderFields() as $gdt)
		{
			if ($gdt->hasInput())
			{
				$orders[$gdt->getName()] = $gdt->getInput();
			}
		}
		if (empty($orders) && $defaultOrder)
		{
			foreach (explode(',', $defaultOrder) as $order)
			{
				$order = trim($order);
				$col = Strings::substrTo($order, ' ', $order);
				$order = stripos($order, ' DESC') ? 0 : 1;
				$orders[$col] = $order;
			}
		}
		return $orders;
	}
	
	protected function make_cmp(array $sorting)
	{
		$headers = $this->headers;
		return function (GDO $a, GDO $b) use (&$sorting, &$headers)
		{
			foreach ($sorting as $column => $sortDir)
			{
			    if ($gdt = $headers->getField($column))
			    {
    			    if ($diff = $gdt->gdoCompare($a, $b))
    			    {
    					return $sortDir ? $diff : -$diff;
    			    }
			    }
			}
			return 0;
		};
	}
	
}
