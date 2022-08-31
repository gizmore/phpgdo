<?php
namespace GDO\Table\tpl;
/** @var $pagemenu \GDO\Table\GDT_PageMenu **/
/** @var $pages \GDO\Table\PageMenuItem[] **/
?>
<div class="gdo-pagemenu">
<ul>
<?php foreach ($pages as $page) : ?>
<li class="<?=$page->htmlClass()?>"><a href="<?=$page->href?>" rel="<?=$pagemenu->relationForPage($page)?>"><?=$page->page?></a></li>
<?php endforeach; ?>
</ul>
</div>
