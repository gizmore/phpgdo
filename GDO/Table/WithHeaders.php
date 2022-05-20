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
	public function makeHeaders() : GDT_Fields
	{
		if (!isset($this->headers))
		{
			$this->headers = GDT_Fields::make($this->nextOrderName());
		}
		return $this->headers;
	}

	public function addHeaders(array $fields) : self
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
		$this->makeHeaders()->addField($field);
		return $this;
	}
	
	public function getOrdersInput() : array
	{
		return Arrays::arrayed($this->headers->getInput());
	}
	
	public function getFiltersInput() : array
	{
		return Arrays::arrayed($this->headers->getInput());
	}
	
	##############################
	### REQUEST container name ###
	##############################
	public $headerName = null;
	public function headerName($headerName)
	{
		$this->headerName = $headerName;
		return $this;
	}
	
	public static $ORDER_NAME = 0;
	public function nextOrderName()
	{
		return $this->headerName ? $this->headerName : ("o" . (++self::$ORDER_NAME));
	}
	
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
		# Get order from request
// 	    if ($orders = Common::getRequestArray($this->headers->name))
// 	    {
// 	        $orders = Arrays::arrayed(@$orders['o']);
// 	    }

		$orders = $this->getOrdersInput();
	    
	    if (empty($orders) && $defaultOrder)
	    {
	        $col = Strings::substrTo($defaultOrder, ' ', $defaultOrder);
	        $order = stripos($defaultOrder, ' DESC') ? '0' : '1';
	        $orders[$col] = $order;
	        $this->headers->input($orders);
// 	        $_REQUEST[$this->headers->name]['o'] = $orders[$col];
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
		$orders = $this->getOrdersInput();
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
