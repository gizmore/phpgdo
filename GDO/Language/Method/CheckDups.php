<?php
namespace GDO\Language\Method;

use GDO\Core\GDT;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Language\Trans;

final class CheckDups extends MethodForm
{

    /**
     * @var string[] Known ok duplicates, because not re-used or overwritten
     */
    private static array $EXCEPTIONS = [
        'btn_download',
        'err_password_retype',
        'err_not_in_room',
        'keywords',
        'link_settings',
        'owner',
        'parent',
        'password_retype',
        'pm_welcome_title',
        'pm_welcome_message',
        'restricted',
        'signature',
        'sitename',
    ];

    protected function createForm(GDT_Form $form): void
    {
        $form->actions()->addFields(
            GDT_Submit::make(),
            GDT_AntiCSRF::make(),
        );
    }

    public function formValidated(GDT_Form $form): GDT
    {
        foreach (Trans::$PATHS as $path1)
        {
            foreach (Trans::$PATHS as $path2)
            {
                if ($path1 !== $path2)
                {
                    $path1a = "{$path1}_en.php";
                    $path2a = "{$path2}_en.php";
                    $data1a = require $path1a;
                    $data2a = require $path2a;

                    $dups = array_intersect(array_keys($data1a), array_keys($data2a));
                    if (count($dups))
                    {
                        foreach ($dups as $dup)
                        {
                            if (!in_array($dup, self::$EXCEPTIONS, true))
                            {
                                $this->error('%s', [sprintf('There is a duplicate key in the lang file for %s and %s: %s', $path1a, $path2a, $dup)]);
                            }
                        }
                    }
                }
            }
        }
        return $this->message('%s', ['All done']);
    }
}
