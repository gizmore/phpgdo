<?php
namespace GDO\Tests;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\Method;

/**
 * The Fuzzer can create permutations for methods.
 *
 * @author gizmore
 */
final class Fuzzer extends AutomatedTestCase
{

	public function getPermutations(Method $method)
	{
		$this->plugMethod($method);
		return $this->plugVariants;
	}


	############
	### Stub ###
	############
	protected function runMethodTest(GDT_MethodTest $mt): void {}


	protected function runGDOTest(GDO $gdo): void {}


	protected function runGDTTest(GDT $gdt): void {}


	protected function getTestName(): string {}

}
