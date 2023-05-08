<?php
declare(strict_types=1);
namespace GDO\Tests;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_Method;
use GDO\Form\GDT_Form;
use GDO\User\GDO_User;

/**
 * Helper Class to test GDOv7 methods.
 * Holds global user objects for test cases.
 * This is ensured by a quirky and important module priority and dependency graph.
 *
 * @version 7.0.3
 * @since 6.11.2
 * @author gizmore
 */
final class GDT_MethodTest extends GDT_Method
{

	# 0) 2-gizmore (admin)
	# 1) 3-Peter   (staff)
	# 2) 4-Monica  (member)
	# 3) 5-Gaston  (guest)
	# 4) 6-Sven    (staff)
	# 5) 7-Darla   (deleted)

	/**
	 * Store some users here for testing.
	 *
	 * @var GDO_User[]
	 */
	public static array $TEST_USERS = [];

	############
	### Exec ###
	############
	public function execute(string $button = null): GDT
	{
		$this->inputs = $this->inputs ?? [];
//		if (!$button)
//		{
//			$button = $this->method->getAutoButton();
//		}
		if ($button)
		{
			$this->inputs[$button] = '1';
			Application::instance()->verb(GDT_Form::POST);
		}
		return parent::execute();
	}

}
