<?php
namespace GDO\Table\Test;

use GDO\Core\GDT_String;
use GDO\Table\GDT_Order;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

/**
 * Unit tests for the Table module.
 *
 * @version 7.0.0
 * @author gizmore
 */
final class TableTest extends TestCase
{

	public function testTableWithArrayResult()
	{
		assertTrue(true); # @TODO implement some test for Module_Table
	}

	public function testMultiOrderHrefCycle(): void
	{
		$alpha = GDT_String::make('alpha');
		$beta = GDT_String::make('beta');
		$order = GDT_Order::make('_o')->
			href('/table.html?_lang=en')-
			setFields([$alpha, $beta])->
			inputs(['_o' => 'alpha ASC,beta DESC'])->
			orders(['alpha' => true, 'beta' => false]);

		assertStringContainsString('_o=alpha+DESC%2Cbeta+DESC', $order->nextHref($alpha));
		assertStringContainsString('_o=alpha+ASC%2Cbeta+ASC', $order->nextHref($beta));
		$order->inputs(['_o' => 'alpha ASC'])->orders(['alpha' => true]);
		assertStringContainsString('_o=alpha+ASC%2Cbeta+ASC', $order->nextHref($beta));
		$order->inputs(['_o' => 'alpha DESC,beta DESC'])->orders(['alpha' => false, 'beta' => false]);
		assertStringContainsString('_o=beta+DESC', $order->nextHref($alpha));
	}

}
