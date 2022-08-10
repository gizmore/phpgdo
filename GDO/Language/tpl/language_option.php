<?php
namespace GDO\Language\tpl\choice;
use GDO\Language\GDO_Language;
/** @var $language GDO_Language **/
if ($language)
{
	echo $language->renderName();
}
else 
{
	echo t('unknown_language');
}
