<?php
namespace GDO\Core;

/**
 * Add a placeholder attribute.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.5.2
 */
trait WithPlaceholder
{
	public string $placeholderRaw;
	public string $placeholderKey;
	public array $placeholderArgs;
	
	public function placeholder(string $key, array $args=null): static
	{
		unset($this->placeholderRaw);
		$this->placeholderKey = $key;
		$this->placeholderArgs = $args;
		return $this;
	}
	
	public function placeholderRaw(string $placeholder): static
	{
		$this->placeholderRaw = $placeholder;
		unset($this->placeholderKey);
		unset($this->placeholderArgs);
		return $this;
	}
	
	/**
	 * Remove any placeholder.
	 */
	public function placeholderNone(): static
	{
		unset($this->placeholderRaw);
		unset($this->placeholderKey);
		unset($this->placeholderArgs);
		return $this;
	}
	
	##############
	### Render ###
	##############
	/**
	 * Render placeholder text.
	 */
	public function renderPlaceholder() : string
	{
		if (isset($this->placeholderKey))
		{
			return html(t($this->placeholderKey, $this->placeholderArgs));
		}
		elseif (isset($this->placeholderRaw))
		{
			return html($this->placeholderRaw);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Render html placeholder attribute.
	 */
	public function htmlPlaceholder() : string
	{
		return isset($this->placeholder) ?
		sprintf(' placeholder="%s"', $this->renderPlaceholder()) :
		'';
	}
	
}
