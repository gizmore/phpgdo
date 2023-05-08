<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Object;
use GDO\Core\Method;
use GDO\Core\WithObject;

/**
 * Abstract method to render a single GDO as a card.
 *
 * @version 7.0.3
 * @since 6.6.4
 * @author gizmore
 */
abstract class MethodCard extends Method
{

	use WithObject;

	private GDO $object;

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make($this->idName())->table($this->gdoTable())->notNull(),
		];
	}

	/**
	 * Parameter name.
	 */
	public function idName(): string { return 'id'; }

	# #############
	# ## Params ###
	# #############

	abstract public function gdoTable(): GDO;

	public function isTrivial(): bool
	{
		return false;
	}

	public function execute(): GDT
	{
		$gdo = $this->getObject();
		return $this->executeFor($gdo);
	}

	# ###########
	# ## Exec ###
	# ###########

	public function getObject(): ?GDO
	{
		if (!isset($this->object))
		{
			$this->object = $this->gdoParameterValue($this->idName());
		}
		return $this->object;
	}

	public function executeFor(GDO $gdo): GDT
	{
		return $this->getCard($gdo);
	}

	public function getCard(GDO $gdo = null): GDT_Card
	{
		$gdo = $gdo ?: $this->getObject();
		$card = GDT_Card::make()->gdo($gdo);
		$this->createCard($card);
		$this->callCardHook($card);
		return $card;
	}

	/**
	 * Override this method to setup your card.
	 */
	protected function createCard(GDT_Card $card): void {}

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
	public function getMethodTitle(): string
	{
		if ($gdo = $this->getObject())
		{
			return $gdo->renderName();
		}
		return parent::getMethodTitle();
	}

}
