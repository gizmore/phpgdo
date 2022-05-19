<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * This GDT renders it's GDO as card.
 *  
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.0
 */
final class GDT_CardView extends GDT
{
	public function renderCell() : string
	{
		return $this->gdo->renderCard();
	}
	
	public function renderCard() : string
	{
		return $this->gdo->renderCard();
	}
	
}
