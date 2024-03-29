<?php
namespace GDO\Net;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;

final class GDO_Domain extends GDO
{

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('domain_id'),
			GDT_DomainName::make('domain_name')->tldOnly(),
		];
	}

}
