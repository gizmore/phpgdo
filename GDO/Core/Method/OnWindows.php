<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * These are the install instructions for the phpgdo project.
 * In CLI rendering it actually just works.
 * When eval the code from this page with php and bash,
 * you have a working phpgdo installation.
 *
 * @version 7.0.3
 * @since 7.0.2
 * @example php -r 'echo eval(fopen(base64_decode("aHR0cHM6Ly9waHBnZG8uY29tL2NvcmUvZ2RvL2ZvcmsvMTMzNw=="),"r"));'
 *
 * @author gizmore
 */
final class OnWindows extends MethodPage
{

	public function getMethodTitle(): string
	{
		return 'phpgdo on Windows';
	}

	public function getMethodDescription(): string
	{
		return t('md_on_windows');
	}

}
