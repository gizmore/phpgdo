<?php
namespace GDO\Table\tpl;
use GDO\Core\GDT_Template;
/** @var $field \GDO\Table\GDT_List **/

echo GDT_Template::php('Table', 'list_filter.php', ['field' => $field]);

$result = $field->getResult();

$pagemenu = $field->pagemenu;
$pages = $pagemenu ? $pagemenu->render() : '';
?>
<?=$pages?>
<div class="gdt-list-card">
<?php if ($field->hasTitle()) : ?>
<h3 class="gdt-headline"><?=$field->renderTitle()?></h3>
<?php endif; ?>
<ul>
<?php
// $template = $field->getItemTemplate();
// if ($field->fetchInto)
// {
	$gdo = $field->fetchAs->cache->getDummy();
	while ($gdo = $result->fetchInto($gdo))
    {
        echo "<li>\n";
        echo $gdo->renderCard();
        echo "</gdoli>\n";
    }
// }
// else
// {
//     while ($gdo = $result->fetchObject())
//     {
//         echo "<li>\n";
//         echo $gdo->renderCard();
//         echo "</li>\n";
//     }
// }
// ?>
</ul>
</div>
<?=$pages?>
