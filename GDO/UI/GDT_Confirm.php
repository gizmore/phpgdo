<?php
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * A field that forces you to re-type a certain text.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.7
 */
final class GDT_Confirm extends GDT_String
{
	public string $confirmation = 'iconfirm';
	
	public function confirmation(string $confirmation): static
	{
		$this->confirmation = $confirmation;
		return $this->label('please_confirm_with', [t($confirmation)]);
	} 
	
	public function validate($value) : bool
	{
		return $this->confirmation === $value ? true : $this->error('err_confirm', [t($this->confirmation)]);
	}
	
}
