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
                    $path1 = "{$path1}_en.php";
                    $path2 = "{$path2}_en.php";
                    $data1 = require $path1;
                    $data2 = require $path2;
                    
                }
            }
        }
        return $this->message('%s', ['All done']);
    }
}
