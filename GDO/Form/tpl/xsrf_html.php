<?php
namespace GDO\Form\tpl;

use GDO\Form\GDT_AntiCSRF;

/** @var $field GDT_AntiCSRF * */
?>
<div class="gdt-container<?=$field->classError()?>">
    <input
            type="hidden"
		<?=$field->htmlName()?>
            value="<?=$field->token?>"/>
	<?=$field->htmlError()?>
</div>
