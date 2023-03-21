<?php
namespace GDO\Install\tpl\cell;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Template;

/** @var $field GDT_Template * */
/** @var $module GDO_Module * */
$module = $field->gdo;
$class = 'gdt-module-license'
?>
<span class="<?=$class?>"><?=$module->license?></span>
