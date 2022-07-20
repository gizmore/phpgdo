<?php
namespace GDO\UI;

use GDO\Form\GDT_Submit;

/**
 * A delete button confirms before the action is executed.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.8.0
 */
class GDT_DeleteButton extends GDT_Submit
{
	public string $icon = 'delete';
	public function defaultLabel() : self { return $this->label('btn_delete'); }
	public function getDefaultName() : string { return 'delete'; }
	
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
