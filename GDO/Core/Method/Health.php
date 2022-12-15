<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\GDT_HealthCard;

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
