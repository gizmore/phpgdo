<?php
namespace GDO\Net;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;

final class GDO_SubDomain extends GDO
{
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('subdomain_id'),
			GDT_Domain::make('subdomain_domain'),
			GDT_DomainName::make('subdomain_name'),
		];
	}
	
}
