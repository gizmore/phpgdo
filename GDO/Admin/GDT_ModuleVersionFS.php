<?php
declare(strict_types=1);
namespace GDO\Admin;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Version;

/**
 * The filesystem version of a module.
 *
 * @version 7.0.3
 * @since 6.3.0
 * @author gizmore
 */
final class GDT_ModuleVersionFS extends GDT_Version
{

	public function gdo(?GDO $gdo): GDT
	{
		$this->var = $gdo->version;
		return $this;
	}

	public function gdoCompare(GDO $a, GDO $b): int
	{
		$va = $a->version;
		$vb = $b->version;
		return $va === $vb ? 0 : ($va < $vb ? -1 : 1);
	}

	public function getVar(): string|array|null
	{
		$m = $this->getModule();
		return $m ? $m->version : '0';
	}

}
