<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;
use GDO\UI\GDT_Card;

/**
 * A list item.
 * Has a title, subtitle, subtext, image and menu.
 * @author gizmore
 * @version 7.0.0
 * @since 6.7.0
 */
final class GDT_ListItem extends GDT_Card
{
	public $right;
	public function right($content) { $this->right = $content; return $this; }
	
	public function renderCell() : string
	{
		return GDT_Template::php('Table', 'list_item.php', ['gdt'=>$this]); }
	
// 	public function render() : string
// 	{
// 	    switch (Application::$INSTANCE->getFormat())
// 	    {
// 	        case 'json': 
// 	            GDT_List::$CURRENT->data[] = $this->renderJSON();
// 	            break;
// 	        case 'xml':
//                 GDT_List::$CURRENT->data[] = $this->renderJSON();
//                 break;
// 	        default:
// 	            return $this->renderCell();
// 	    }
// 	}
	
	public function renderJSON()
	{
	    $data = [];
	    if ($this->title)
	    {
	        $data['title'] = $this->title->renderCell();
	    }
	    if ($this->subtitle)
	    {
	        $data['subtitle'] = $this->subtitle->renderCell();
	    }
	    if ($this->content)
	    {
	        $data['content'] = $this->content->renderCell();
	    }
	    if ($this->right)
	    {
	        $data['right'] = $this->right->renderCell();
	    }
	    if ($this->subtext)
	    {
	        $data['subtext'] = $this->subtext->renderCell();
	    }
	    return $data;
	}
	
}
