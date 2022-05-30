<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

final class Welcome extends MethodPage
{
	public function getMethodTitle() : string
	{
		return t('welcome');
	}
	
}
