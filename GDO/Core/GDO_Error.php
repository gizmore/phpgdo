<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * An exception with translated error message.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDO_Error extends GDO_Exception
{

	public string $key;
	public ?array $args;

	public function __construct(string $key, array $args = null, $code = self::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
		$this->key = $key;
		$this->args = $args;
	}

}
