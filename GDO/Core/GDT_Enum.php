<?php
namespace GDO\Core;

/**
 * An enum.
 * It is a select with special rendering.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Enum extends GDT_Select
{
	public array $enumValues;
	public function enumValues(string...$enumValues) : self
	{
		$this->enumValues = $enumValues;
		return $this;
	}
	
	public function getChoices()
	{
		if (isset($this->enumValues))
		{
			return array_combine($this->enumValues, $this->enumValues);
		}
		return [];
	}
	
	public function enumIndex()
	{
		return $this->enumIndexFor($this->getVar());
	}
	
	public function enumIndexFor($enumValue)
	{
		$index = array_search($enumValue, $this->enumValues, true);
		return $index === false ? 0 : $index + 1;
	}

	public function configJSON() : array
	{
		return [
			'name' => $this->getName(),
			'enumValues' => isset($this->enumValues) ? $this->enumValues : null,
			'selected' => $this->getVar(),
			'notNull' => $this->notNull,
		];
	}
	
	##############
	### Render ###
	##############
	public function displayVar(string $var=null) : string
	{
		return $var === null ? '' : t('enum_' . $var);
	}
	
	public function gdoExampleVars() : ?string
	{
		return implode('|', $this->enumValues);
	}

	################
	### DB Field ###
	################
	public function gdoColumnDefine() : string
	{
		$values = implode(',', array_map(array('GDO\Core\GDO', 'quoteS'), $this->enumValues));
		return "{$this->identifier()} ENUM ($values) CHARSET ascii COLLATE ascii_bin {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

}
