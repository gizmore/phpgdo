<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithGDO;

/**
 * This GDT renders a GDO as a card.
 *
 * @version 7.0.1
 * @since 6.10.0
 * @author gizmore
 * @deprecated Why?
 */
final class GDT_CardView extends GDT
{

	use WithGDO;

	public function isTestable(): bool { return false; }

	public function renderHTML(): string
	{
		return $this->gdo->renderCard();
	}

}
