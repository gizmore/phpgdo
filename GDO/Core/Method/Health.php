<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_HealthCard;
use GDO\Core\Method;

/**
 * Display service health information.
 *
 * @author gizmore
 * @see Module_Hydra
 */
final class Health extends Method
{

	public function getMethodTitle(): string
	{
		return t('health');
	}

	public function execute()
	{
		return GDT_HealthCard::make('health');
	}

}
