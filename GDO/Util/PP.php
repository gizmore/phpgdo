<?php
namespace GDO\Util;

use gizmore\pp\Preprocessor;

require 'php-preprocessor/src/Preprocessor.php';

/**
 * php-preprocessor bindings for phpgdo.
 *
 * @since 7.0.2
 * @author gizmore
 */
final class PP
{

	/**
	 * Used to include it.
	 */
	public static function init(): Preprocessor
	{
		return new Preprocessor();
	}

}
