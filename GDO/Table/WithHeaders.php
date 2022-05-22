<?php
namespace GDO\Table;

use GDO\Core\GDT_Fields;
use GDO\DB\ArrayResult;
use GDO\Core\GDT;
use GDO\Core\GDO;
use GDO\Util\Strings;
use GDO\Util\Arrays;

/**
 * A trait for tables and list which adds an extra headers variable. This has to be a \GDO\Core\GDT_Fields.
 * Implements @\GDO\Core\ArrayResult multisort for use in @\GDO\Table\MethodTable.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.5.0
 */
trait WithHeaders
{
	/**
	 * @var GDT_Fields
	 */
	public GDT_Fields $headers;
	
	/**
	 * @return GDT_Fields
	 */
	public function makeHeaders() : self
	{
		if (!isset($this->headers))
		{
			$this->headers = GDT_Fields::make();
		}
		return $this;
	}

	public function addHeaders(GDT...$fields) : self
	{
		if (count($fields))
		{
			$this->makeHeaders();
			$this->headers->addFields(...$fields);
		}
		return $this;
	}
	
	public function addHeader(GDT $field) : self
	{
		$this->makeHeaders();
		$this->headers->addField($field);
		return $this;
	}
	
// 	##############
// 	### Inputs ###
// 	##############
// 	public function getOrdersInput() : array
// 	{
// 		return $this->headers->inputs;
// 	}
	
// 	public function getFiltersInput() : array
// 	{
// 		return $this->headers->getInput();
// 	}
	
	##############################
	### REQUEST container name ###
	##############################
// 	public $headerName = null;
// 	public function headerName($headerName)
// 	{
// 		$this->headerName = $headerName;
// 		return $this;
// 	}
	
// 	public static $ORDER_NAME = 0;
// 	public function nextOrderName()
// 	{
// 		return $this->headerName ? $this->headerName : ("o" . (++self::$ORDER_NAME));
// 	}
	
	###############
	### Ordered ###
	###############
	/**
	 * PHP Sorting is unstable.
	 * This method does a stable multisort on an ArrayResult.
	 * @param ArrayResult $result
	 * @return ArrayResult
	 */
	public function multisort(ArrayResult $result, $defaultOrder=null)
	{
		$orders = [];# $this->getOrdersInput();
	    
	    if (empty($orders) && $defaultOrder)
	    {
	        $col = Strings::substrTo($defaultOrder, ' ', $defaultOrder);
	        $order = stripos($defaultOrder, ' DESC') ? '0' : '1';
	        $orders[$col] = $order;
	        $this->headers->inputs($orders);
	    }
		
		# Build sort func
		$sort = $this->make_cmp($orders);
		
		# Use it
		uasort($result->getData(), $sort);
		
		return $result;
	}
	
	/**
	 * Create a comperator function.
	 */
	private function make_cmp() : callable
	{
		$headers = $this->headers;
		$orders = $this->headers->inputs;
		return function (GDO $a, GDO $b) use (&$orders, &$headers)
		{
			foreach ($orders as $column => $sortDir)
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
