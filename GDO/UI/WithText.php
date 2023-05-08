<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds text attributes, either raw or from a lang file.
 * By default, text is not escaped upon rendering.
 *
 * Adds text($key, $args) for I18n version
 * Adds textRaw($text) for raw version
 * Adds textEscaped() for skipping escaping.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 * @see /GDO7.php - for global functions
 */
trait WithText
{

	public string $textRaw;
	public string $textKey;
	public ?array $textArgs;
	public bool $textEscaped = false;


	public static function makeTexted(string $key, array $args = null): static
	{
		return static::make()->text($key, $args);
	}


	public function text(?string $key, array $args = null, bool $escaped = false): self
	{
		unset($this->textRaw);
		if (!$key)
		{
			unset($this->textKey);
		}
		else
		{
			$this->textKey = $key;
		}
		$this->textArgs = $args;
		return $this->textEscaped($escaped);
	}

	public function textUnescaped(bool $unescaped = true): self
	{
		return $this->textEscaped(!$unescaped);
	}

	public function textEscaped(bool $escaped = true): self
	{
		$this->textEscaped = $escaped;
		return $this;
	}

	public function textArgs(string...$args): static
	{
		$this->textArgs = count($args) ? $args : null;
		return $this;
	}

	public function textRaw(?string $text, bool $esacped = false): self
	{
		unset($this->textRaw);
		if ($text)
		{
			$this->textRaw = $text;
		}
		unset($this->textKey);
		unset($this->textArgs);
		return $this->textEscaped($esacped);
	}

	public function textNone(): self
	{
		unset($this->textRaw);
		unset($this->textKey);
		unset($this->textArgs);
		return $this->textUnescaped();
	}

	##############
	### Render ###
	##############

	public function hasText(): bool
	{
		return isset($this->textKey) || isset($this->textRaw);
	}

	public function renderText(): string
	{
		if (isset($this->textKey))
		{
			$txt = t($this->textKey, $this->textArgs);
		}
		elseif (isset($this->textRaw))
		{
			$txt = $this->textRaw;
		}
		else
		{
			return GDT::EMPTY_STRING;
		}
		return $this->textEscaped ? html($txt) : $txt;
	}

}
