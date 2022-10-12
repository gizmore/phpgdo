<?php
namespace GDO\Core;

/**
 * A DB Exception causes a 500 error.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.3
 */
final class GDO_DBException extends GDO_ErrorFatal
{
	public function __construct(string $key, array $args=null, int $code=500)
	{
		parent::__construct($key, $args, $code);
	}
	
}
