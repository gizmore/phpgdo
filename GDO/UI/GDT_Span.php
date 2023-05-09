<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A single piece of text.
 *
 * @see GDT_Element
 * @see WithPHPJQuery
 */
final class GDT_Span extends GDT_Element
{

	protected function tagName(): string
	{
		return 'span';
	}

}
