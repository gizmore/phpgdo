<?php
namespace GDO\Core;

/**
 * Named identifier.
 * Is unique among their table and case-i ascii.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
class GDT_Name extends GDT_String
{
	use WithGDO;
	
	public function defaultLabel() : self
	{
		return $this->label('name');
	}

	const LENGTH = 64;
	
	public int $min = 2;
	public int $max = self::LENGTH;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = false;
	public string $pattern = "/^[A-Za-z][-A-Za-z _0-9;:]{1,63}$/sD";
// 	public bool $notNull = true;
	public bool $unique = true;
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
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
	
	public function renderCLI() : string
	{
	    return $this->renderHTML() . "\n";
	}
	
	public function renderJSON()
	{
	    return $this->renderHTML();
	}
	
	public function plugVars() : array
	{
		static $plugNum = 0; # @TODO: meh :( I'd like to have some scheme here, but meh
		$plugNum++;
		return [
			[$this->getName() => "Name_$plugNum"],
		];
	}
	
}
