<?php
namespace GDO\Table\tpl;

use GDO\Table\GDT_List;
use GDO\Table\GDT_ListItem;
use GDO\UI\GDT_SearchField;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;

/** @var $field GDT_List **/

echo GDT_Template::php('Table', 'list_filter.php', ['field' => $field]);

###################
### Search Form ###
###################
if ($field->searched)
{
	$formSearch = GDT_Form::make($field->headers->name)->slim()->verb('GET');
	$formSearch->addField(GDT_SearchField::make('search'));
	$formSearch->actions()->addField(GDT_Submit::make()->css('display', 'none'));
	echo $formSearch->render();
}

############
### List ###
############
$pagemenu = @$field->pagemenu;
$pagemenu = $pagemenu ? $pagemenu->renderCell() : '';

if (!$field->countItems())
{
    if ($field->hideEmpty)
    {
        return;
    }
}

$result = $field->getResult();

echo $pagemenu;
?>
<!-- Begin List -->
<div class="gdt-list">
<?php if ($field->hasTitle()) : ?>
  <div class="gdt-list-title">
    <h3><?=$field->renderTitle()?></h3>
  </div>
<?php endif; ?>
<?php
$dummy = $field->fetchAs->cache->getDummy();
$li = GDT_ListItem::make();
while ($gdo = $result->fetchInto($dummy)) :
  echo $gdo->renderList();
endwhile;
?>
</div>
<!-- End of List -->
<?php
echo $pagemenu;
