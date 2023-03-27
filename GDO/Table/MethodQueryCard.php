<?php
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\Method;
use GDO\UI\GDT_HTML;

/**
 * This method renders a GDO as card.
 *
 * @version 7.0.1
 * @since 6.3.0
 * @author gizmore
 */
abstract class MethodQueryCard extends Method
{

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('id')->table($this->gdoTable())->notNull(),
		];
	}

	/**
	 * @return GDO
	 */
	abstract public function gdoTable();

	public function execute(): GDT
	{
		$html = $this->renderCard();
		return GDT_HTML::make()->var($html);
	}

	public function renderCard(): string
	{
		$object = $this->getQueryCard();
		return $object->renderCard();
	}

	public function getQueryCard(): GDO
	{
		return $this->gdoParameterValue('id');
	}

}
