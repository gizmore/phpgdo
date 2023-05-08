<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds a title to a GDT.
 * This title is not rendered with a H tag.
 *
 * @version 7.0.3
 * @since 6.0.1
 * @author gizmore
 * @see GDT_Headline
 */
trait WithTitle
{

	public string $titleRaw;
	public string $titleKey;
	public ?array $titleArgs;
	public bool $titleEscaped = false;

	public function title(string $key, array $args = null, bool $escaped = false): static
	{
		unset($this->titleRaw);
		$this->titleKey = $key;
		$this->titleArgs = $args;
		return $this->titleEscaped($escaped);
	}

	public function titleRaw(?string $title, bool $escaped = true): static
	{
		unset($this->titleRaw);
		unset($this->titleKey);
		unset($this->titleArgs);
		if ($title)
		{
			$this->titleRaw = $title;
		}
		return $this->titleEscaped($escaped);
	}

	public function titleEscaped(bool $escaped = true): static
	{
		$this->titleEscaped = $escaped;
		return $this;
	}

	public function titleNone(): static
	{
		unset($this->titleRaw);
		unset($this->titleKey);
		unset($this->titleArgs);
		return $this;
	}

	##############
	### Render ###
	##############
	public function hasTitle(): bool
	{
		return isset($this->titleKey) || isset($this->titleRaw);
	}

	public function renderTitle(): string
	{
		$t = GDT::EMPTY_STRING;
		if (isset($this->titleKey))
		{
			$t = t($this->titleKey, $this->titleArgs);
		}
		elseif (isset($this->titleRaw))
		{
			$t = $this->titleRaw;
		}
		return $this->titleEscaped ? html($t) : $t;
	}

}
