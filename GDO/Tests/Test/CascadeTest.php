<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Language\GDO_Language;

/**
 * Test foreign keys and related functionality.
 * 
 * @since 7.0.0
 */
final class CascadeTest extends TestCase
{
	public function testCascadeRestrict()
	{
		GDO_Language::getById('en')->delete();
	}
	
}
