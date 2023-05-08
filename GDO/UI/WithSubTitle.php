<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds a subtitle to a GDT.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see WithTitle
 * @see GDT_Title
 * @see GDT_Headline
 */
trait WithSubTitle
{

	public string $subtitleRaw;
	public string $subtitleKey;
	public ?array $subtitleArgs;
	public bool $subtitleEscaped = false;

	public function subtitle(string $key, array $args = null, bool $escaped = false): self
	{
		unset($this->subtitleRaw);
		$this->subtitleKey = $key;
		$this->subtitleArgs = $args;
		return $this->subtitleEscaped($escaped);
	}

	public function subtitleRaw(?string $subtitle, bool $escaped = false): self
	{
		unset($this->subtitleRaw);
		if ($subtitle)
		{
			$this->subtitleRaw = $subtitle;
		}
		unset($this->subtitleKey);
		unset($this->subtitleArgs);
		return $this->subtitleEscaped($escaped);
	}

	public function noSubTitle(): self
	{
		unset($this->subtitleRaw);
		unset($this->subtitleKey);
		unset($this->subtitleArgs);
		return $this->subtitleEscaped(false);
	}

	public function subtitleEscaped(bool $escaped = true): self
	{
		$this->subtitleEscaped = $escaped;
		return $this;
	}

	##############
	### Render ###
	##############
	public function hasSubTitle(): bool
	{
		return isset($this->subtitleKey) || isset($this->subtitleRaw);
	}

	public function renderSubTitle(): string
	{
		$t = GDT::EMPTY_STRING;
		if (isset($this->subtitleKey))
		{
			$t = t($this->subtitleKey, $this->subtitleArgs);
		}
		elseif (isset($this->subtitleRaw))
		{
			$t = $this->subtitleRaw;
		}
		return $this->subtitleEscaped ? html($t) : $t;
	}

}
