<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\CLI\CLI;
use GDO\Language\Trans;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertLessThanOrEqual;

/**
 * The last test that is run checks if lang files were fully there, or if entries are missing.
 */
final class ZLangTest extends TestCase
{

	public function testLanguageFilesForCompletion()
	{
		if (Trans::$MISS)
		{
			sort(Trans::$MISSING);
			$this->message(CLI::bold('The following lang keys are missing:'));
			foreach (Trans::$MISSING as $key)
			{
				echo " - $key\n";
			}
			flush();
			ob_flush();
		}

		assertLessThanOrEqual(1, count(Trans::$MISSING), 'Assert that (almost) no internationalization was missing.');
	}

}
