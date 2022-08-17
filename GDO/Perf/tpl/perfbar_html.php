<?php
namespace GDO\Perf\tpl;
use GDO\Perf\GDT_PerfBar;
/** @var $bar GDT_PerfBar **/
$i = GDT_PerfBar::data();
printf('<span class="gdo-perf-bar">');
printf('<i>%d&nbsp;Log</i>|<i>%d&nbsp;Qry</i>|<i>%d&nbsp;Wr</i>|<b>%d&nbsp;Tr</b> - ',
	$i['logWrites'], $i['dbQueries'], $i['dbWrites'], $i['dbCommits']);
printf('<i>%.03fs&nbsp;DB</i>+<i>%.03fs&nbsp;PHP</i>=<b>%.03fs</b> - ',
	$i['dbTime'], $i['phpTime'], $i['totalTime']);
printf('<b>%.02f&nbsp;MB</b>|<b>%d&nbsp;Func</b>|<b>%d&nbsp;Alloc</b> - ',
	$i['memory_max']/(1024*1024), $i['funcCount'], $i['allocs']);
printf('<i>%d&nbsp;Classes</i>|<i>%d&nbsp;gdoClasses</i>|<i>%d(<b>%d</b>)&nbsp;GDT</i>|<i>%d(<b>%d</b>)&nbsp;GDO</i>|<i>%d&nbsp;mod</i>|<i>%d&nbsp;langfs</i> - ',
	$i['phpClasses'], $i['gdoFiles'], $i['gdtCount'], $i['gdtPeakCount'], $i['gdoCount'], $i['gdoPeakCount'], $i['gdoModules'], $i['gdoLangFiles']);
printf('<b>%d&nbsp;tmpl</b>|<b title="Hooks">%d&nbsp;hook</b>|<b>%d&nbsp;ipc</b>|<b>%d&nbsp;mail</b> - ',
	$i['gdoTemplates'], $i['gdoHooks'], $i['gdoIPC'], $i['gdoMails']);
printf('<b>%d/%d&nbsp;cache</b>',
	$i['fileCacheHits'], $i['fileCacheRq']);
printf('</span>');
