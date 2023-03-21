<?php
namespace GDO\Core;

/**
 * This table holds IPC shim data.
 * The IPC shim uses a DB table to communicate with other processes.
 * data is simply stored as a json message.
 *
 * @version 7.0.1
 * @since 6.5.0
 * @author gizmore@wechall.net
 * @see GDT_Hook
 * @see GWS_Server
 */
final class GDO_Hook extends GDO
{

	public function gdoEngine(): string { return GDO::MYISAM; }

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('hook_id'),
			GDT_JSON::make('hook_message')->notNull()->max(2048),
		];
	}

}
