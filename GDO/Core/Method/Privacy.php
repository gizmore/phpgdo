<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\GDT_Tuple;

/**
 * Show the privacy informational page. 
 * 
 * @version 7.0.1
 * @since 6.8.0
 * @author gizmore
 */
final class Privacy extends MethodPage
{
    
	public function getMethodTitle() : string
	{
		return t('privacy');
	}
	
	public function getMethodDescription() : string
	{
		return t('privacy_settings');
	}
	
	public function execute()
	{
		return GDT_Tuple::make()->addFields(
			$this->pageTemplate(),
			PrivacyToggles::make()->execute(),
		);
	}
	
}
