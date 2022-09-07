<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\WithFileCache;

/**
 * Show the impressum informational page.
 * Is file cached.
 *
 * @version 7.0.1
 * @since 6.8.0
 * @author gizmore
 * @see MethodPage
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
