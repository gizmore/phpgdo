<?php
namespace GDO\Perf\Method;

use GDO\Core\Method;
use GDO\Perf\GDT_PerfBar;

/**
 * Render performance statistics.
 * 
 * @version 7.0.0
 * @since 6.2.0
 * @author gizmore
 */
final class Bar extends Method
{
	public function execute()
    {
        return GDT_PerfBar::make();
    }
    
}
