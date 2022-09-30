<?php
namespace GDO\UI\tpl;
use GDO\Core\Javascript;
use GDO\Core\Website;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Loading;
use GDO\Language\Trans;
/** @var $page GDT_Page **/
?>
<!DOCTYPE html>
<html lang="<?=Trans::$ISO?>">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=Website::displayTitle()?></title>
    <meta property="og:title" content="<?=sitename()?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="generator" content="GDO v<?=Module_Core::GDO_REVISION?>">
	<?=Website::displayHead()?>
	<?=Website::displayMeta()?>
	<?=Website::displayLink()?>
  </head>
  <body>
    
	<div id="gdo-pagewrap">
	
	  <header id="gdo-header"><?=$page->topBar()->render()?></header>
	
	  <div class="gdo-body">
		<div class="gdo-main">
          <?=$page->topResponse()->render()?>
		  <?=isset($page->html)?$page->html:''?>
		</div>
	  </div>

	  <footer id="gdo-footer"><?=$page->bottomBar()->render()?></footer>
	
	</div>
	<?=GDT_Loading::make()->render()?>
	<?=Javascript::displayJavascripts()?>
  </body>
</html>
