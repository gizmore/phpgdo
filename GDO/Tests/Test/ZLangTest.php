<?php
namespace GDO\Tests\Test;

use GDO\CLI\CLI;
use GDO\Language\Trans;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertLessThanOrEqual;

final class ZLangTest extends TestCase
{
	public function testLanguageFilesForCompletion()
	{
		if (Trans::$MISS)
		{
			$this->message(CLI::bold("The following lang keys are missing:"));
			foreach (Trans::$MISSING as $key)
			{
				echo " - $key\n";
			}
			ob_flush();
		}
		
		assertLessThanOrEqual(1, count(Trans::$MISSING), 'Assert that (almost) no internationalization was missing.');
	}
	
}
