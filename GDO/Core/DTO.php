<?php
namespace GDO\Core;

/**
 * DataTransferObjects are not abstract but do not save to DB.
 * Currently only GDO_Profile does use it, to transfer profile data via Websocket API.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 */
abstract class DTO extends GDO
{

	public function gdoDTO(): bool
	{
		return true;
	}

	public function gdoCached(): bool
	{
		return false;
	}

	public function isTestable(): bool
	{
		return false;
	}

}
