<?php
namespace GDO\Core;

use GDO\Form\WithFormAttributes;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;
use GDO\Form\GDT_Form;

/**
 * A GDT with user input.
 * 
 * Fields have a name and an initial/var/value.
 * Fields have an optional error message.
 * Fields can be nullable.
 * Fields can have an icon, a label and a placeholder.
 * Fields can have various form attributes.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
abstract class GDT_Field extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithValue;
	use WithError;
	use WithInput;
	use WithPHPJQuery;
	use WithPlaceholder;
	use WithFormAttributes;
	
	##################
	### Name Label ###
	##################
	public function defaultLabel() : self
	{
		if ($name = $this->getName())
		{
			return $this->label($name);
		}
		return $this;
	}
	
	public function gdoColumnNames() : array
	{
		return [$this->name];
	}
	
	################
	### Creation ###
	################
	public function blankData() : array
	{
		if ($key = $this->getName())
		{
			return [$key => $this->getVar()];
		}
		return GDT::EMPTY_ARRAY;
	}
	
	############
	### Data ###
	############
	public function getGDOData() : array
	{
		return [$this->name => $this->getVar()];
	}

	public function setGDOData(array $data) : self
	{
		if (isset($data[$this->name]))
		{
			return $this->var($data[$this->name]);
		}
		return $this->var($this->initial);
	}
	
	public function configJSON() : array
	{
		return [
			'name' => $this->getName(),
		];
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return $this->validateNull($value);
	}
	
	public function isRequired() : bool
	{
		return $this->notNull;
	}
	
	public function classError() : string
	{
		return $this->hasError() ? ' has-error' : '';
	}
	
	#######################
	### Input/Var/Value ###
	#######################
	public function inputToVar($input) : ?string
	{
		if (is_string($input))
		{
// 			$input = trim($input);
			return $input === '' ? null : $input;
		}
		if ($input instanceof GDT_Method)
		{
			return $input->execute()->renderCLI();
		}
		if (is_array($input))
		{
			return json_encode($input);
		}
		return null;
// 		return $this->initial;
	}
	
	public function getVar()
	{
		$name = $this->getName();
		if (isset($this->inputs[$name]))
		{
			$input = $this->inputs[$name];
			return $this->inputToVar($input);
		}
		return $this->var;
	}
	
	################
	### Features ###
	################
	public bool $orderable  = true; # 
	public bool $aclcapable = true; # produces 3 ACL settings per GDT
	public bool $searchable = true; # is searched during big searches
	public bool $filterable = true; #
	public function isOrderable() :  bool { return $this->orderable; }
	public function isACLCapable() : bool { return $this->aclcapable; }
	public function isSearchable() : bool { return $this->searchable; }
	public function isFilterable() : bool { return $this->filterable; }
	public function isSerializable():bool { return true; }
	
	public function orderable(bool $orderable) : self
	{
		$this->orderable = $orderable;
		return $this;
	}
	
	public function noacl() : self { return $this->aclcapable(false); }
	public function aclcapable(bool $aclcapable) : self
	{
		$this->aclcapable = $aclcapable;
		return $this;
	}
	
	public function searchable(bool $searchable) : self
	{
		$this->searchable = $searchable;
		return $this;
	}
	
	public function filterable(bool $filterable) : self
	{
		$this->filterable = $filterable;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function htmlFocus() : ?string
	{
		if ($form = GDT_Form::$CURRENT)
		{
			if ($form->focus)
			{
				if ($this->notNull)
				{
					if ($this->getVar() === null)
					{
						if ($this->notNull)
						{
							return ' gdo-focus-required';
						}
						else
						{
							return ' gdo-focus';
						}
					}
				}
			}
		}
		return null;
	}
	
	public function renderList() : string
	{
		$text = $this->renderLabelText();
		if ($text)
		{
			$text .= ':&nbsp;';
		}
		$text .= $this->displayVar($this->getVar());
		return "<div>$text</div>";
	}
	
	public function renderCard() : string
	{
		return $this->displayCard($this->renderHTML());
	}
	
	public function displayCard($var) : string
	{
		return sprintf("<label>%s%s:&nbsp;</label><span>%s</span>\n",
			$this->htmlIcon(), $this->renderLabelText(), $var);
	}

}
