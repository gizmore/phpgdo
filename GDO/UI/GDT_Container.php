<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * Simple collection of GDTs.
 * The render functions call the render function on all fields.
 * No template is loaded for this class.
 * No template is used yet.
 * Has no input.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.7.1
 * @see GDT_Panel
 */
class GDT_Container extends GDT
{
	const HORIZONTAL = 1;
	const VERTICAL = 2;
	
    use WithFlex;
	use WithFields;
	use WithPHPJQuery;
	
	public function renderCLI() : string
	{
		$newline = $this->flexDirection === self::HORIZONTAL ? ' ' : "\n";
		$rendered = '';
		if (isset($this->fields))
		{
			foreach ($this->fields as $field)
			{
				$rendered .= $field->renderCLI();
				$rendered .= $newline;
			}
		}
		return $rendered;
	}
	
	private function setupHTML()
	{
	    $this->addClass('gdt-container');
	    if ($this->flex)
	    {
	    	$this->addClass('flx '.$this->flexClass());
	        if ($this->flexCollapse)
	        {
	            $this->addClass('flx-collapse');
	        }
	    }
	}
	
// 	public function renderCell() : string
// 	{
// 	    if ($this->fields)
// 	    {
//     	    $this->setupHTML();
//     		$back = '<div '.$this->htmlID().' '.$this->htmlAttributes().'>';
//     		foreach ($this->fields as $gdt)
//     		{
//     			$back .= $gdt->renderCell();
//     		}
//     		$back .= '</div>';
//     		return $back;
// 	    }
// 	}
	
// 	public function renderCLI() : string
// 	{
// 	    return $this->renderCLIFields();
// 	}
	
	public function renderForm() : string
	{
		if (isset($this->fields))
	    {
	        $this->setupHTML();
	        $back = '<div '.$this->htmlID().' '.$this->htmlAttributes().'>';
    	    foreach ($this->fields as $gdt)
    	    {
    	        $back .= $gdt->renderForm();
    	    }
    	    $back .= '</div>';
    	    return $back;
	    }
	    return '';
	}
	
// 	public function renderCard() : string
// 	{
// 	    if ($this->fields)
// 	    {
// 	        $this->setupHTML();
// 	        $back = '<div '.$this->htmlID().' '.$this->htmlAttributes().'>';
//     	    foreach ($this->fields as $gdt)
//     	    {
//     	        $back .= $gdt->renderCard();
//     	    }
//     	    $back .= '</div>';
//     	    return $back;
// 	    }
// 	}
	
}
