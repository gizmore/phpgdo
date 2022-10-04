<?php
namespace GDO\Table\tpl;

use GDO\Form\GDT_Form;
use GDO\Core\GDT_Select;
use GDO\Form\GDT_Submit;
use GDO\UI\GDT_Accordeon;
use GDO\UI\GDT_SearchField;
use GDO\Table\GDT_List;

/**
 * @var $field GDT_List
 */
###################
### Search Form ###
###################
if (isset($field->headers))
{
    # The list search criteria form.
    $frm = GDT_Form::make($field->headers->name)->verb('GET');
    
    # Searchable input
    if ($field->searched)
    {
        $searchable = [];
        foreach ($field->headers->getAllFields() as $gdt)
        {
            if ($gdt->isSearchable())
            {
                $searchable[] = $gdt;
            }
        }
        if (count($searchable))
        {
            $frm->addField(GDT_SearchField::make('search'));
        }
    }
    
    # Orderable select
    if ($field->isOrdered())
    {
        $orderable = [];
        foreach ($field->headers->getFields() as $gdt)
        {
            if ($gdt->isOrderable())
            {
                if (!$gdt->hidden)
                {
                    $orderable[$gdt->name] = $gdt->renderLabel();
                }
            }
        }
        
        if (count($orderable))
        {
        	$n = $field->order->name;
            $select = GDT_Select::make("{$n}_by")->label('order_by');
            $select->choices($orderable);
            $select->initial($field->order->getOrderBy());
            $frm->addField($select);
            
            $ascdesc = GDT_Select::make("{$n}_dir")->icon('arrow_up')->label('order_dir');
            $ascdesc->choices['ASC'] = t('asc');
            $ascdesc->choices['DESC'] = t('desc');
            $ascdesc->initial($field->order->getOrderDir());
            $frm->addField($ascdesc);
        }
    }
    
    if ($field->isFilterable())
    {
        # Not supported yet
    }
    
    # Show quicksearch form in accordeon
    if (count($frm->getFields()))
    {
        $frm->actions()->addField(GDT_Submit::make());
        $accordeon = GDT_Accordeon::make()->addField($frm)->titleRaw($frm->displaySearchCriteria());
        echo $accordeon->renderHTML();
    }
    
}
