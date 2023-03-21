<?php
namespace GDO\Table\tpl;

use GDO\Table\GDT_PageMenu;
use GDO\Table\PageMenuItem;

/** @var $pagemenu GDT_PageMenu * */
/** @var $pages PageMenuItem[] * */
?>
<div class="gdo-pagemenu">
    <ul>
		<?php
		foreach ($pages as $page) : ?>
            <li class="<?=$page->htmlClass()?>"><a href="<?=$page->href?>" rel="<?=$pagemenu->relationForPage($page)?>"><?=$page->page?></a></li>
		<?php
		endforeach; ?>
    </ul>
</div>
