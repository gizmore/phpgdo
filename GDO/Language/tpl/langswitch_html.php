<?php
namespace GDO\Language\tpl;
use GDO\Language\GDO_Language;
use GDO\Language\Module_Language;
use GDO\Language\Trans;

$languages = Module_Language::instance()->cfgSupported();
?>
<div class="gdo-lang-switch">
 <form method="post" action="<?=href('Language', 'SwitchLanguage')?>">
  <input type="hidden" name="_mo" value="Language" />
  <input type="hidden" name="_me" value="SwitchLanguage" />
  <input type="hidden" name="_ref" value="<?=html(urldecode($_SERVER['REQUEST_URI']))?>" />
  <label><?=t('language')?></label>
  <select name="lang">
<?php
foreach ($languages as $language)
{
	$language instanceof GDO_Language;
	$sel = Trans::$ISO === $language->getISO() ? ' selected="selected"' : '';
	printf("<option value=\"%s\"%s>%s</option>", $language->getISO(), $sel, $language->renderOption());
}
?>  
  </select>
  <input type="submit" value="<?=t('btn_set')?>" />
 </form>
</div>
