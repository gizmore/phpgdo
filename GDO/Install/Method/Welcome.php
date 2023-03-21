<?php
namespace GDO\Install\Method;

use GDO\UI\MethodPage;

/**
 * Installer welcome page.
 *
 * @version 7.0.0
 * @since 6.0.1
 * @author gizmore
 */
class Welcome extends MethodPage
{

	public function getMethodTitle(): string
	{
		return t('welcome');
	}

	public function getMethodDescription(): string
	{
		return t('md_welcome', [sitename()]);
	}

}
