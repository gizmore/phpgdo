<?php
namespace GDO\Form\tpl;

use GDO\Form\GDT_Hidden;

/** @var GDT_Hidden $field * */
?>
<div>
    <input type="hidden"<?=$field->htmlName()?>
		<?=$field->htmlValue()?> /></div>
