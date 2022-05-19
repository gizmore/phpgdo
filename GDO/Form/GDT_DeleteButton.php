<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;

/**
 * A delete button confirms before the action is executed.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.8.0
 */
class GDT_DeleteButton extends GDT_Submit
{
	public $icon = 'delete';
	public function renderCell() : string { return GDT_Template::php('Form', 'form/delete.php', ['field'=>$this]); }
	public function defaultLabel() : self { return $this->label('btn_delete'); }
	public function defaultName() { return 'delete'; }
	
	############
	### Text ###
	############
	public string $confirmKey = 'confirm_delete';
	public ?array $confirmArgs = null;
	
	public function confirmText($key, array $args=null)
	{
	    $this->confirmKey = $key;
	    $this->confirmArgs = $args;
	    return $this;
	}
	
	public function displayConfirmText()
	{
	    return t($this->confirmKey, $this->confirmArgs);
	}
	
}
