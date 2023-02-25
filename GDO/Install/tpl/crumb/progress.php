<?php
namespace GDO\Install\tpl\crumb;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Util\Math;
use GDO\Util\Common;
use GDO\Install\Config;

$steps = Config::steps();
$step = Math::clampInt(Common::getGetInt('step', 1), 1, count($steps));

$bar = GDT_Bar::make()->horizontal();

foreach (array_keys($steps) as $step)
{
	$step++;
	$link = GDT_Link::make("install_title_$step")->href(Config::hrefStep($step));
	$bar->addField($link);
}

echo $bar->render();
