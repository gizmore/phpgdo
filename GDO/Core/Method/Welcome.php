<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

final class Welcome extends MethodPage
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
