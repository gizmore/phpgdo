<?php
namespace GDO\Tests\Test;

use GDO\Date\Time;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

/**
 * Date and time tests.
 *
 * - check millisecond support
 *
 * @version 7.0.0
 * @since 6.10.1
 * @author gizmore
 */
final class CalendarTest extends TestCase
{

	/**
	 * Test if milliseconds are supported in dates.
	 */
	public function testMilliseconds()
	{
		$date = Time::getDate();
		assertStringContainsString('.', $date, 'Test if date contains a dot for milliseconds.');
		assertStringNotContainsString('.000', $date, 'Test if date has non .000 milliseconds in current date. yaya... this can give a false positive fail. 1 permille chance :)');
	}

}
