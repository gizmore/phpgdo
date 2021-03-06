<?php
namespace GDO\UI\tpl;
/** @var $link \GDO\UI\GDT_Link **/
?>
<span class="<?=$link->htmlClass()?>">
  <a
   <?=$link->htmlDisabled()?>
   <?=$link->htmlID()?>
   <?=$link->htmlAttributes()?>
   <?=$link->htmlTarget()?>
   <?=$link->htmlHREF()?>
   <?=$link->htmlRelation()?>>
     <?=$link->htmlIcon()?>
     <?php if ($link->hasLabel()) : ?>
       <?=$link->renderLabel()?>
     <?php else : ?>
       <?=html(isset($link->href)?$link->href:'---')?>
     <?php endif; ?>
   </a>
</span>
