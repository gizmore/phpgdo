<?php
namespace GDO\Core;

/**
 * The GDT_Text exceeds the limits of a GDT_String.
 * It is **not** displayed as a textarea.
 * Use GDT_Message for a textarea.
 * The cell rendering in tables should be dottet.
 *
 * @author gizmore
 * @version 7.0.2
 * @since 5.0.2
 * @see GDT_String
 * @see GDT_Message
 */
class GDT_Text extends GDT_String
{

	public int $max = 65535;

	public function defaultLabel(): static
	{
		return $this->label('message');
	}

	# ###############
	# ## Validate ###
	# ###############
	public function validate($value): bool
	{
		return parent::validate($value) ? $this->validateNonNumeric($value) : false;
	}

	public function validateNonNumeric($value): bool
	{
		return is_numeric($value) ? $this->error('err_text_only_numeric') : true;
	}

}
