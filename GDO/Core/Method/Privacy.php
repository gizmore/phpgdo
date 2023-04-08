<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Tuple;
use GDO\UI\MethodPage;

/**
 * Show the privacy informational page.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore
 */
final class Privacy extends MethodPage
{

	public function getMethodTitle(): string
	{
		return t('privacy');
	}

	public function getMethodDescription(): string
	{
		return t('privacy_settings');
	}

	public function execute(): GDT
	{
		return GDT_Tuple::make()->addFields(
			$this->pageTemplate(),
			PrivacyToggles::make()->execute(),
		);
	}

}
