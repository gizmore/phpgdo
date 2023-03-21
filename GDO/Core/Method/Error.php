<?php
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\UI\MethodPage;

/**
 * Render an arbitrary error.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class Error extends MethodPage
{

	public function isTrivial(): bool { return false; } # Auto-Test's for 200 code, so not trivial to test.

	public function gdoParameters(): array
	{
		return [
			GDT_String::make('error')->notNull(),
		];
	}

	public function execute(): GDT
	{
		$error = $this->gdoParameterVar('error');
		return $this->error('error', [html($error)]);
	}

}
