<h2><?=t('install_title_9');?></h2>
<?php

use GDO\Form\GDT_Form;
use GDO\Install\Config;
use GDO\UI\GDT_Panel;

/** @var $form GDT_Form * */
echo $form->render();

echo GDT_Panel::make()->text('copy_htaccess_info', [Config::linkStep(10)])->render();
