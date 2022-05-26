<?php
namespace GDO\Date\Method;

use GDO\Core\GDT_String;
use GDO\Form\GDT_Validator;
use GDO\Form\GDT_Form;
use GDO\Date\GDO_Timezone;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;

/**
 * Detect timezone by name.
 * Call Timezone method with resolved id.
 *
 * @author gizmore
 */
final class TimezoneDetect extends MethodForm
{
	public function formName() { return 'tzform'; }
	
	public function isUserRequired() : bool{ return false; }
	public function isTransactional() : bool { return false; }
	
	public function createForm(GDT_Form $form) : void
	{
		$tz = GDT_String::make('timezone')->notNull();
		$form->addFields(
			$tz,
			GDT_Validator::make('validTimezone')->validator($form, $tz, [$this, 'validateTimezoneName']),
		);
		$form->actions()->addField(GDT_Submit::make()->label('btn_set'));
	}
	
	public function validateTimezoneName(GDT_Form $form, GDT_String $string, $value)
	{
		if (!($this->tz = GDO_Timezone::getBy('tz_name', $value)))
		{
			return $string->error('err_unknown_timezone');
		}
		return true;
	}
	
	public function formValidated(GDT_Form $form)
	{
		$_REQUEST['tzform']['timezone'] = $this->tz->getID();
		return Timezone::make()->execute($form);
	}

}
