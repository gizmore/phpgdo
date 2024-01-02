<?php
namespace GDO\UI\Method;

use GDO\Core\GDO_ArgError;
use GDO\Core\GDT;
use GDO\Core\GDT_UInt;
use GDO\Core\MethodAjax;
use GDO\User\GDO_User;

final class Resolution extends MethodAjax
{

    public function isUserRequired(): bool
    {
        return true;
    }

    public function gdoParameters(): array
    {
        return [
            GDT_UInt::make('w')->notNull(),
            GDT_UInt::make('h')->notNull(),
        ];
    }

    /**
     * @throws GDO_ArgError
     */
    public function execute(): GDT
    {
        $user = GDO_User::current();
        $user->saveSettingVar('UI', 'device_width', $this->gdoParameterVar('w'));
        $user->saveSettingVar('UI', 'device_height', $this->gdoParameterVar('h'));
        return $this->message('msg_device_dimension_saved');
    }

}

