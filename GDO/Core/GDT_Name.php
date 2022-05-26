<?php
namespace GDO\Core;

/**
 * Named identifier.
 * Is unique among their table and case-s ascii.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
class GDT_Name extends GDT_String
{
	use WithGDO;
	
	public function plugVar() : string { return 'Name_' . self::$COUNT; }
	
	public function defaultLabel() : self { return $this->label('name'); }

	const LENGTH = 64;
	
	public int $min = 2;
	public int $max = self::LENGTH;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[A-Za-z][-A-Za-z _0-9;:]{1,63}$/sD";
	public bool $notNull = true;
	public bool $unique = true;
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
	    if ($this->gdo)
	    {
	        return $this->gdo->renderName();
	    }
	    return $this->display();
	}
	
	public function renderCLI() : string
	{
	    return $this->renderCell();
	}
	
	public function renderJSON()
	{
	    return $this->renderCell();
	}
	
}
