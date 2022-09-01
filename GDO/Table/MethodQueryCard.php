<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\UI\GDT_HTML;

/**
 * This method renders a GDO as card.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.3.0
 */
abstract class MethodQueryCard extends Method
{
	/**
	 * @return GDO
	 */
	public abstract function gdoTable();
	
	public function gdoParameters() : array
	{
		return [
			GDT_Object::make('id')->table($this->gdoTable())->notNull(),
		];
	}
	
	public function getQueryCard() : GDO
	{
		return $this->gdoParameterValue('id');
	}
	
	public function execute()
	{
		$html = $this->renderCard();
		return GDT_HTML::make()->var($html);
	}
	
	public function renderCard() : string
	{
		$object = $this->getQueryCard();
		return $object->renderCard();
	}
	
}
