<?php
namespace GDO\UI\tpl;
use GDO\Core\GDT;

/** @var \GDO\UI\GDT_Box $field **/
?>
<div<?=$field->htmlAttributes()?>>
    <p><?=$field->renderFields(GDT::RENDER_HTML)?></p>
</div>
