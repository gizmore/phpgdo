<?php
namespace GDO\Install\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertFileExists;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

/**
 * Test the gdo_adm.sh tool.
 *
 * @since 7.0.0
 * @author gizmore
 */
final class AdmTest extends TestCase
{

	public function testSystemTest()
	{
		$result = $this->proc('php gdo_adm.php systemtest');
		assertStringContainsString('Your system is able to run GDO', $result, 'Test if systemtest is working.');
	}

	public function testConfigGeneration()
	{
		assertTrue(rename('protected/config_test.php', 'protected/config_test2.php'), 'Test protected folder for writability');

		$this->proc('php gdo_adm.php configure');
		assertFileExists('protected/config.php', 'Test if config can be written.');

		$result = $this->proc('php gdo_adm.php test');
		assertStringContainsString('Your configuration seems solid.', $result, 'Test if config writing was solid.');
	}

}
