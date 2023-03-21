<?php
namespace GDO\Language\tpl;

use GDO\Language\GDO_Language;

/** @var $language GDO_Language * */
if ($language)
{
	echo $language->renderName();
}
else
{
	echo t('unknown_language');
}
