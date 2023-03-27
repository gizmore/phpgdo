<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithName;

/**
 * A field that is an additional validator for a field.
 * A validator can be applied to a field and specify a method.
 * The method gets the form, the field, and the field's value to call error on the field.
 *
 * @version 7.0.1
 * @since 5.0.0
 * @author gizmore
 * @see GDT_Form
 */
class GDT_Validator extends GDT
{

	use WithName;

	public GDT_Form $validatorForm;
	public GDT $validatorField;
	public array $validator;

	/**
	 * Dummy signature
	 */
	public function validator_func_dummy(GDT_Form $form, GDT $field, $value) {}

	###########
	### GDT ###
	###########

	/**
	 * so it gets evaluated in the validation process.
	 */
	public function isWriteable(): bool { return true; }

	/**
	 * So it is not shown in CLI and stuff.
	 */
	public function isHidden(): bool { return true; }

	public function hasInput(): bool
	{
		return true; # this triggers validation code
	}

	public function validatorFor(GDT_Form $form, string $fieldName, callable $validator): self
	{
		$field = $form->getField($fieldName);
		return $this->validator($form, $field, $validator);
	}

	public function validator(GDT_Form $form, ?GDT $field, callable $validator): self
	{
		$this->validatorForm = $form;
		$this->validatorField = $field ? $field : $this;
		$this->validator = $validator;
		return $this;
	}

	public function validateInput($input): bool
	{
		$field = $this->validatorField;
		$value = $field->getValue();
		return $this->validate($value);
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		$field = $this->validatorField;
		return call_user_func($this->validator, $this->validatorForm, $field, $field->getValue());
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		if (isset($this->validatorField))
		{
			if ($this->validatorField === $this)
			{
				return GDT_Template::php('Form', 'validator_form.php', ['field' => $this]);
			}
		}
		return GDT::EMPTY_STRING;
	}

}
