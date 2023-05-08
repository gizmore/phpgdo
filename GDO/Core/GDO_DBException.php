<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * A DB Exception causes a 500 error.
 *
 * @version 7.0.3
 * @since 5.0.3
 * @author gizmore
 */
final class GDO_DBException extends GDO_Exception
{

	public string $query;
	public int $errcode;
	public string $errmsg;


	public function __construct(int $errcode, string $errmsg, string $query, \Throwable $previous = null)
	{
		parent::__construct('err_db', [$errcode, $errmsg, $query], self::DB_ERROR_CODE, $previous);
		$this->errcode = $errcode;
		$this->errmsg = $errmsg;
		$this->query = $query;
	}

}
