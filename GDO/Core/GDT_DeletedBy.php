<?php
namespace GDO\Core;

use GDO\User\GDT_User;

/**
 * GDT signals deletion for a row.
 * Some stuff auto-detects that.
 * Not often used yet.
 *
 * @version 7.0.1
 * @since 6.11.2
 * @author gizmore
 */
final class GDT_DeletedBy extends GDT_User
{

	public bool $writeable = false;

	public function isHidden(): bool { return true; }

	public function gdtDefaultLabel(): ?string
    {
        return 'deleted_by';
    }

	public function plugVars(): array
	{
		return [
			[$this->getName() => null],
		];
	}

}
