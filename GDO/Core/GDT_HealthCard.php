<?php
namespace GDO\Core;

use GDO\UI\GDT_Card;
use GDO\Util\Load;

/**
 * Render service health information.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class GDT_HealthCard extends GDT_Card
{
	
	protected function __construct()
	{
		parent::__construct();
		Load::update();
		$this->title('health');
		$this->addFields(
			GDT_Version::make('gdo_version')->initial(Module_Core::GDO_VERSION),
			GDT_String::make('gdo_revision')->initial(Module_Core::GDO_REVISION),
			GDT_Version::make('php_version')->initial(PHP_VERSION),
			GDT_UInt::make('health_cpus')->initial(Load::getLoadMax()),
			GDT_Percent::make('health_load')->digitsAfter(2)->initial(Load::getLoadAvg()),
			GDT_Filesize::make('health_mem')->label('health_mem')->initial(Load::getMemAvail()),
			GDT_Filesize::make('health_free')->label('health_free')->initial(Load::getMemFree()),
		);
	}
	
}
