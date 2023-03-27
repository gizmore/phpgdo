<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * The GDT_Text exceeds the limits of a GDT_String.
 * It is **not** displayed as a textarea.
 * Use GDT_Message for a textarea.
 * The cell rendering in tables should be dottet.
 *
 * @version 7.0.3
 * @since 5.0.2
 * @author gizmore
 * @see GDT_Message
 */
class GDT_Text extends GDT_String
{

	public ?int $max = 65535;

	public function defaultLabel(): self
	{
		return $this->label('message');
	}

	# ###############
	# ## Validate ###
	# ###############
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		return parent::validate($value) && $this->validateNonNumeric($value);
	}

	public function validateNonNumeric($value): bool
	{
		return !is_numeric($value) || $this->error('err_text_only_numeric');
	}

}
