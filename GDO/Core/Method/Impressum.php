<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Show the impressum informational page.
 *
 * @version 6.10.1
 * @since 6.8.0
 * @author gizmore
 */
final class Impressum extends MethodPage
{
	public function getMethodTitle() : string
	{
		return t('impressum');
	}
	
	public function getMethodDescription() : string
	{
		return t('md_impressum', [sitename()]);
	}
	
}
