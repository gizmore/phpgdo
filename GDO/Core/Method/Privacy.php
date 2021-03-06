<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Show the privacy informational page. 
 * 
 * @version 6.10.2
 * @since 6.8.0
 * @author gizmore
 */
final class Privacy extends MethodPage {
    
	public function getMethodTitle() : string
	{
		return t('privacy');
	}
	
	public function getMethodDescription() : string
	{
		return t('md_privacy', [sitename()]);
	}
	
}
