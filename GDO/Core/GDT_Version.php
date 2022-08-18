<?php
namespace GDO\Core;

/**
 * GDT_Version field.
 * 
 * The $var is "Major.Minor.Patch". 
 * The $value is a \GDO\Core\Version
 * 
 * Validation via GDT_String::$pattern
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see Version
 * @see GDT_String
 */
class GDT_Version extends GDT_String
{
	public int $min = 5;
	public int $max = 14;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^\\d+\\.\\d+\\.\\d+$/iD";
	
	###################
	### Var / Value ###
	###################
	/**
	 * @param Version $value
	 */
	public function toVar($value) : ?string
	{
		return $value ? $value->__toString() : null;
	}
	
	public function toValue($var = null)
	{
		return $var ? new Version($var) : null;
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => Module_Core::GDO_VERSION],
		];
	}

	##############
	### Render ###
	##############
	public function renderCell() : string
	{
		$var = $this->getVar();
		return $var === null ? GDT::EMPTY_STRING : $var;
	}
	
}
