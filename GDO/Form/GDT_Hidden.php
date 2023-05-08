<?php
declare(strict_types=1);
namespace GDO\Form;

use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;

/**
 * An HTML Hidden Form Fields.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 */
class GDT_Hidden extends GDT_String
{

	public bool $writeable = false;

	public function isHidden(): bool { return true; }

	public function renderForm(): string
	{
		return GDT_Template::php('Form', 'hidden_html.php', ['field' => $this]);
	}

}
