<?php
namespace GDO\Form;

use GDO\Core\GDT;

/**
 * A field that is an additional validator for a field.
 * A validator can be applied to a field and specify a method.
 * The method gets the form, the field, and the field's value to call error on the field.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see GDT_Form
 */
class GDT_Validator extends GDT
{
	public function isWriteable() : bool { return true; } # so it gets evaluated in the validation process.
	
	/**
	 * Dummy signature
	 */
	public function validator_func_dummy(GDT_Form $form, GDT $field, $value) {}

	public function hasInputs() : bool
	{
		return true; # this triggers validation code
	}
	
	###########
	### GDT ###
	###########
	public GDT_Form $validatorForm;
	public GDT $validatorField;
	public array $validator;
	
	public function validator(GDT_Form $form, GDT $field, callable $validator) : self
	{
		$this->validatorForm = $form;
		$this->validatorField = $field;
		$this->validator = $validator;
		return $this;
	}
	
	public function validateInput($input) : bool
	{
		$field = $this->validatorField;
		$value = $field->getValue();
		return $this->validate($value);
	}
	
	public function validate($value) : bool
	{
		$field = $this->validatorField;
		return call_user_func($this->validator, $this->validatorForm, $field, $value);
	}
	
}
