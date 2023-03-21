<?php
namespace GDO\Core;

use GDO\UI\GDT_Card;
use GDO\Util\Load;

/**
 * Render service health information.
 *
 * @since 7.0.1
 * @author gizmore
 */
final class GDT_HealthCard extends GDT_Card
{

	protected function __construct()
	{
		parent::__construct();
		Load::init();
		$this->title('health');
		$this->addFields(
			GDT_Version::make('gdo_version')->initial(Module_Core::GDO_VERSION),
			GDT_String::make('gdo_revision')->initial(Module_Core::GDO_REVISION),
			GDT_Version::make('php_version')->initial(PHP_VERSION),
			GDT_UInt::make('health_cpus')->initial(Load::$STATE['cpus']),
			GDT_Percent::make('health_load')->digitsAfter(2)->initial(Load::$STATE['load']),
			GDT_Decimal::make('health_clock')->digits(2, 2)->initial(Load::$STATE['clock']),
			GDT_Filesize::make('health_mem')->label('health_mem')->initial(Load::$STATE['avail']),
			GDT_Filesize::make('health_used')->label('health_used')->initial(Load::$STATE['used']),
			GDT_Filesize::make('health_free')->label('health_free')->initial(Load::$STATE['free']),
			GDT_Filesize::make('health_hdd_total')->label('health_hdd_avail')->initial(Load::$STATE['hdda']),
			GDT_Filesize::make('health_hdd_used')->label('health_hdd_used')->initial(Load::$STATE['hddu']),
			GDT_Filesize::make('health_hdd_free')->label('health_hdd_free')->initial(Load::$STATE['hddf']),
		);
	}

}
