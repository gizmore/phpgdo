<?php
namespace GDO\Core;

/**
 * Named identifier.
 * Is unique among their table and case-i ascii.
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
 */
class GDT_Name extends GDT_String
{

	use WithGDO;

	public const LENGTH = 64;
	public int $min = 2;
	public int $max = self::LENGTH;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = false;
	public string $pattern = '/^[A-Za-z][-A-Za-z _0-9;:]{1,63}$/sD';
	public bool $unique = true;

// 	public bool $notNull = true;

	public function defaultLabel(): self
	{
		return $this->label('name');
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		if (isset($this->gdo))
		{
			return $this->gdo->renderName();
		}
		if ($var = $this->getVar())
		{
			return html($var);
		}
		return GDT::EMPTY_STRING;
	}

	public function renderCLI(): string
	{
		return $this->renderHTML() . "\n";
	}

	public function renderJSON()
	{
		return $this->renderHTML();
	}

	public function plugVars(): array
	{
		static $plugNum = 0; # @TODO: meh :( I'd like to have some scheme here, but meh
		$plugNum++;
		return [
			[$this->getName() => "Name_$plugNum"],
		];
	}

}
