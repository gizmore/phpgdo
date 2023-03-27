<?php
namespace GDO\Language\Method;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;
use GDO\Language\Trans;

/**
 * Get all translation data for the current language.
 * Javascript applications can use this.
 *
 * @version 7.0.2
 * @since 6.2.0
 * @author gizmore
 */
final class GetTransData extends MethodAjax
{

	public function getMethodTitle(): string
	{
		return 'Translation Data';
	}

	public function getMethodDescription(): string
	{
		return 'Get Translation Data via Ajax for JS.';
	}

	public function execute(): GDT
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
		hdr('Content-Type: text/javascript');
		if (!Application::$INSTANCE->isUnitTests())
		{
			echo $js;
		}
		return Application::exit();
	}

}
