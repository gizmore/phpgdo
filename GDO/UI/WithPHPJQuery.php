<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Extend a GDT with an API similiar to jQuery.
 * Render with htmlAttributes().
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.7.0
 */
trait WithPHPJQuery
{
	#######################
	### HTML Attributes ###
	#######################
	public array $htmlAttributes;
	
	public function attr(string $attribute, $value=null)
	{
		if (!isset($this->htmlAttributes))
		{
			$this->htmlAttributes = [];
		}
		if ($value === null)
		{
			return isset($this->htmlAttributes[$attribute]) ? 
			    $this->htmlAttributes[$attribute] : GDT::EMPTY_STRING;
		}
		$this->htmlAttributes[$attribute] = $value;
		return $this;
	}
	
	public function htmlAttributes() : string
	{
		$html = '';
		if (isset($this->htmlAttributes))
		{
			foreach ($this->htmlAttributes as $attribute => $value)
			{
				$html .= " $attribute=\"$value\"";
			}
		}
		return $html;
	}

	public function addClass(string $class) : self
	{
		# Old classes
		$classes = explode(" ", trim($this->attr('class')));
		
		# Merge new classes
		$newclss = explode(" ", $class); # multiple possible
		foreach ($newclss as $class)
		{
		    if ($class = trim($class))
		    {
    			if (!in_array($class, $classes, true))
    			{
    				$classes[] = $class;
    			}
		    }
		}
		
		return $this->attr('class', implode(" ", $classes));
	}
	
	###########
	### CSS ###
	###########
	private array $css;
	
	public function css(string $attr, $value=null)
	{
		if (!isset($this->css))
		{
			$this->css = [];
		}
		if ($value === null)
		{
			return @$this->css[$attr];
		}
		$this->css[$attr] = $value;
		return $this->updateCSS();
	}
	
	private function updateCSS()
	{
		$rules = '';
		foreach ($this->css as $key => $value)
		{
			$rules .= "$key: $value; ";
		}
		return $this->attr('style', $rules);
	}

}
