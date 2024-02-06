<?php
namespace GDO\Core;

use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * The "CeatedBy" column is filled with current user upon creation.
 * In case the installer or maybe cli is running, the system user is used.
 *
 * (: sdoʌǝp ˙sɹɯ ɹoɟ ƃuıʞoo⅂
 *
 * @version 6.10.3
 * @since 5.0.0
 * @author gizmore
 */
final class GDT_CreatedBy extends GDT_User
{

	public bool $writeable = false;

	protected function __construct()
	{
		parent::__construct();
// 		$this->withCompletion();
	}

	public function gdtDefaultLabel(): ?string
    {
        return 'created_by';
    }

    /**
     * Initial data.
     * Force persistance on current user.
     * @throws GDO_Exception
     */
	public function blankData(): array
	{
		if ($this->var)
		{
			return [$this->name => $this->var];
		}
		$user = GDO_User::current();
		if (Application::$INSTANCE->isInstall() || (!$user->isPersisted()))
		{
			$user = GDO_User::system();
		}
		return [$this->name => $user->getID()];
	}

    /**
     * @throws GDO_Exception
     */
    public function getValue(): mixed
	{
		$value = parent::getValue();
		return $value ?: GDO_User::system();
	}

}
