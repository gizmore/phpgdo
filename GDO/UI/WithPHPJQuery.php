<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Extend a GDT with an API similiar to jQuery.
 * Render all HTML attributes with htmlAttributes().
 * Currently only a very small subset is implemented,
 * as you do not do that fancy DOM manipulations.
 *
 * Implemented:
 *
 * - addClass()
 * - attr()
 * - css()
 *
 * @version 7.0.1
 * @since 6.7.0
 * @author gizmore
 */
trait WithPHPJQuery
{

	#######################
	### HTML Attributes ###
	#######################
	/**
	 * @var string[string]
	 */
	public array $htmlAttributes;
	/**
	 * @var string[string]
	 */
	private array $css;

	/**
	 * The returned html string has a leading space.
	 */
	public function htmlAttributes(): string
	{
		$html = '';
		if (isset($this->htmlAttributes))
		{
			foreach ($this->htmlAttributes as $attr => $var)
			{
				$html .= " $attr=\"$var\"";
			}
		}
		return $html;
	}	/**
	 * Change an attribute.
	 *
	 * @return self|string
	 */
	public function attr(string $attr, string $value = null)
	{
		if (!isset($this->htmlAttributes))
		{
			$this->htmlAttributes = [];
		}
		if ($value === null)
		{
			return isset($this->htmlAttributes[$attr]) ?
				$this->htmlAttributes[$attr] : GDT::EMPTY_STRING;
		}
		$this->htmlAttributes[$attr] = $value;
		return $this;
	}

	###################
	### CSS Classes ###
	###################
	public function addClass(string $class): self
	{
		# Old classes
		$s = ' ';
		$classes = explode($s, trim($this->attr('class')));

		# Merge new classes
		$newclss = explode($s, $class); # multiple possible
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

		# Join them
		return $this->attr('class', implode($s, $classes));
	}

	###########
	### CSS ###
	###########


	public function css(string $attr, $value = null)
	{
		if (!isset($this->css))
		{
			$this->css = [];
		}
		if ($value === null)
		{
			return (string)@$this->css[$attr];
		}
		$this->css[$attr] = $value;
		return $this->updateCSS();
	}

	private function updateCSS(): self
	{
		$rules = '';
		foreach ($this->css as $key => $var)
		{
			$rules .= "$key: $var; ";
		}
		return $this->attr('style', trim($rules));
	}

	##############
	### Render ###
	##############


}
