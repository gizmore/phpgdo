<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;

/**
 * Abstract method to render a single GDO as a card.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.6.4
 */
abstract class MethodCard extends Method
{
	public abstract function gdoTable(): GDO;

	/**
	 * Parameter name.
	 */
	public function idName(): string { return 'id'; }

	# #############
	# ## Params ###
	# #############
	public function gdoParameters(): array
	{
		return [
			GDT_Object::make($this->idName())->table($this->gdoTable())->notNull(),
		];
	}

	private GDO $object;
	public function getObject(): ?GDO
	{
		if (!isset($this->object))
		{
			$this->object = $this->gdoParameterValue($this->idName());
		}
		return $this->object;
	}

	# ###########
	# ## Exec ###
	# ###########
	public function execute()
	{
		$gdo = $this->getObject();
		return $this->executeFor($gdo);
	}

	protected function executeFor(GDO $gdo): GDT
	{
		return $this->getCard($gdo);
	}

	public function getCard(GDO $gdo = null): GDT_Card
	{
		$gdo = $gdo ? $gdo : $this->getObject();
		$card = GDT_Card::make()->gdo($gdo);
		$this->createCard($card);
		$this->callCardHook($card);
		return $card;
	}
	
	/**
	 * Override this method to setup your card.
	 */
	protected function createCard(GDT_Card $card): void
	{
	}

	/**
	 * Call the card creation hook for all modules.
	 */
	private function callCardHook(GDT_Card $card): void
	{
		$mo = $this->getModuleName();
		$me = $this->getMethodName();
		GDT_Hook::callHook("CreateCard{$mo}{$me}", $card);
	}

	# ##########
	# ## Seo ###
	# ##########
	public function getMethodTitle() : string
	{
		if ($gdo = $this->getObject())
		{
			return $gdo->renderName();
		}
		return parent::getMethodTitle();
	}

}
