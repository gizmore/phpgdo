<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\WithFileCache;

/**
 * Show the privacy informational page. 
 * 
 * @version 7.0.1
 * @since 6.8.0
 * @author gizmore
 */
final class Privacy extends MethodPage {
    
	use WithFileCache;
	
	public function getMethodTitle() : string
	{
		return t('privacy');
	}
	
	public function getMethodDescription() : string
	{
		return t('md_privacy', [sitename()]);
	}
	
}
