<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\Method;

/**
 * Reply the input back. (ECHO). GDOv7 style =)
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class Ekko extends Method
{

	public function getCLITrigger(): string
	{
		return 'echo';
	}

	public function gdoParameters(): array
	{
		return [
			GDT_String::make('text')->notNull()->labelNone(),
		];
	}

	public function execute(): GDT
	{
		return $this->gdoParameter('text');
	}

}
