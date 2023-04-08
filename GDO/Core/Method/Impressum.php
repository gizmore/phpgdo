<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Show the impressum informational page.
 * Is file cached.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore
 * @see MethodPage
 */
final class Impressum extends MethodPage
{

	public function getMethodTitle(): string
	{
		return t('impressum');
	}

	public function getMethodDescription(): string
	{
		return t('md_impressum', [sitename()]);
	}

}
