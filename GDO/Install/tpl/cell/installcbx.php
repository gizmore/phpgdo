<?php
namespace GDO\Install\tpl\cell;
/**
 * @var $field \GDO\Core\GDT_Template
 * @var $module \GDO\Core\GDO_Module
 */
$module = $field->gdo;
$name = $module->getName();
$checked = isset($_REQUEST['module'][$name]) || $module->isInstalled() || $module->isCoreModule();
$checked = $checked ? 'checked="checked"' : '';
?>
<input
 id="cbx-module-<?=$name?>"
 type="checkbox"
 class="gdo-module-install-cbx"
 onclick="toggledModule(this, '<?=$name?>');"
 name="module[<?=$name?>]"
 <?=$checked?> />
