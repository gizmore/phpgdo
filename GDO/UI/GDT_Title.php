<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * A short utf8 title.
 * Pretty common.
 *
 * NotNull because if we have a title it is mandatory.
 * Also has a nice big T as default icon.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 *
 */
class GDT_Title extends GDT_String
{

	use WithTitle;

	public ?int $min = 2;
	public ?int $max = 128;
	public string $icon = 'title';
	public bool $notNull = true;
	public int $encoding = self::UTF8;
	public bool $caseSensitive = false;

	public function defaultLabel(): self { return $this->label('title'); }

	public function renderCLI(): string
	{
		return $this->renderLabel() . ': ' .
			$this->renderTitle();
	}

	public function renderHTML(): string
	{
		$text = $this->renderTitle();
		$text = $this->titleEscaped ? html($text) : $text;
		return '<h3 class="gdt-title">' . $text . '</h3>';
	}

	public function renderCell(): string
	{
		return $this->renderTitle();
	}

	public function var(?string $var): static
	{
		if ($var === null)
		{
			unset($this->titleRaw);
		}
		else
		{
			$this->titleRaw = $var;
		}
		return parent::var($var);
	}

}
