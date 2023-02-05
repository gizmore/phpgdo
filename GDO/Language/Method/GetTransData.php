<?php
namespace GDO\Language\Method;

use GDO\Language\Trans;
use GDO\Core\Application;
use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;

/**
 * Get all translation data for the current language.
 * Javascript applications can use this.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.2.0
 */
final class GetTransData extends MethodAjax
{
	public function getMethodTitle() : string
	{
		return "Translation Data";
	}
	
	public function getMethodDescription() : string
	{
		return "Get Translation Data via Ajax for JS.";
	}
	
	public function execute()
	{
		$data = Trans::getCache(Trans::$ISO);
		
		# JSON requests are handled by the normal gdo pipeline
		if (Application::$INSTANCE->isJSON())
		{
			return GDT_Array::makeWith($data);
		}
		
		# HTML requests output a javascript markup.
		$langdata = json($data);
	    $js = sprintf('window.GDO_TRANS = {}; window.GDO_TRANS.CACHE = %s;', $langdata);
	    if (!Application::$INSTANCE->isUnitTests())
	    {
	        hdr('Content-Type: text/javascript');
	        die($js);
	    }
	}

}
