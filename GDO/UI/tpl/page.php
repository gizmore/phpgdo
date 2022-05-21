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
    <title><?=Website::displayTitle()?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="generator" content="GDO v<?=Module_Core::GDO_REVISION?>">
	<?=Website::displayHead()?>
	<?=Website::displayMeta()?>
	<?=Website::displayLink()?>
  </head>
  <body>
    
    <input type="checkbox" id="gdo-left-nav" class="gdo-nav" />
    <input type="checkbox" id="gdo-right-nav" class="gdo-nav" />

    <nav id="gdo-left-bar" class="gdo-nav-bar"><?=$page->leftBar()->render()?></nav>
    <label for="gdo-left-nav"></label>

    <nav id="gdo-right-bar" class="gdo-nav-bar"><?=$page->rightBar()->render()?></nav>
    <label for="gdo-right-nav"></label>
  
	<div id="gdo-pagewrap">
	
	  <header id="gdo-header"><?=$page->topBar()->render()?></header>
	
	  <div class="gdo-body">
		<label for="gdo-left-nav" id="gdo-left-nav2"></label>
		<label for="gdo-right-nav" id="gdo-right-nav2"></label>
		<div class="gdo-main">
		  <?=$page->topBar()->render()?>
		  <?=$page->html?>
		</div>
	  </div>

	  <footer id="gdo-footer"><?=$page->bottomBar()->render()?></footer>
	
	</div>
	<?=GDT_Loading::make()->render()?>
	<?=Javascript::displayJavascripts()?>
  </body>
</html>
