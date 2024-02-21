<?php
namespace GDO\Language\tpl;

use GDO\Language\GDO_Language;
use GDO\Language\Module_Language;
use GDO\Language\Trans;

$languages = Module_Language::instance()->cfgSupported();
$ref = urlencode($_SERVER['REQUEST_URI']);
?>
<div class="gdo-lang-switch">
<?php foreach ($languages as $lang) : ?>
<?php
$href = href('Language', 'SwitchLang', sprintf('&_ref=%s&lang=%s&submit=1', $ref, $lang->getISO()));
$flag = $lang->renderFlag();
$alt = t('md_switch_language', [$lang->renderName()]);
?>
    <a href="<?=$href?>" aria-label="<?=$alt?>" title="<?=$alt?>" rel="nofollow"><?=$flag?></a>
    <?php endforeach; ?>
</div>
