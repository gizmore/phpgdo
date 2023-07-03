<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\UI\MethodPage;

/**
 * Render an arbitrary error.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 */
final class Error extends MethodPage
{

	public function isTrivial(): bool { return false; }

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
