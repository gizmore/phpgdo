<?php
namespace GDO\Form;

use GDO\Core\GDT_Template;
use GDO\Core\WithName;
use GDO\UI\GDT_Button;
use GDO\Core\WithValue;
use GDO\Core\WithInput;

/**
 * An input submit button.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.2
 */
class GDT_Submit extends GDT_Button
{
	use WithName;
	use WithInput;
	use WithValue;
	use WithFormAttributes;
	use WithClickHandler;
	
	public function getDefaultName() : ?string
	{
		return 'submit';
	}
	
	public function renderCell() : string
	{
		return GDT_Template::php('Form', 'submit_form.php', ['field' => $this]);
	}
	
	/**
	 * The HTML value of a submit is the button label.
	 */
	public function htmlValue() : string
	{
		return sprintf(' value="%s"', $this->renderLabel());
	}
	
	public function plugVars() : array
	{
		return [
			null, # Test method unclicked.
			'1', # Test method clicked.
		];
	}
	
}
