<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Text;
use GDO\Core\WithFields;
use GDO\Core\WithName;

/**
 * Simple collection of GDTs.
 * The render functions call the render function on all fields.
 * No template is loaded for this class.
 * No template is used yet.
 * Has no input.
 *
 * @version 7.0.3
 * @since 5.7.1
 * @author gizmore
 * @see GDT_Panel
 */
class GDT_Container extends GDT
{

	use WithFlex;
	use WithFields;
    use WithName;
	use WithPHPJQuery;

	final public const HORIZONTAL = 1;
	final public const VERTICAL = 2;

	##############
	### Render ###
	##############
	public function renderFields(int $renderMode): string
	{
		$this->setupHTML();
		$rendered = $this->renderFieldsB($renderMode);
		$attrs = $this->htmlAttributes();
		return "<div{$this->htmlID()}{$attrs}>{$rendered}</div>\n";
	}

	/**
	 * Setup the CSS classes for this container.
	 */
	protected function setupHTML(): void
	{
		$this->addClass('gdt-container container-fluid');
		if ($this->flex)
		{
			$this->addClass("flx {$this->flexClass()}");

			if ($this->flexWrap)
			{
				$this->addClass('flx-wrap');
			}

			if ($this->flexShrink)
			{
				$this->addClass('flx-shrink');
			}
		}
	}

    public function renderTelegram(): string
    {
        return $this->renderCLI();
    }

    public function renderCLI(): string
	{
		$rendered = '';
		$newline = $this->flexDirection === self::HORIZONTAL ? ' | ' : "\n";
		if (isset($this->fields))
		{
			$first = true;
			foreach ($this->fields as $field)
			{
				if ($first)
				{
					$first = false;
				}
				else
				{
					$rendered .= $newline;
				}
				$rendered .= $field->renderCLI();
			}
			return $rendered;
		}
		return GDT::EMPTY_STRING;
	}

	/**
	 * Add a @see GDT_Span to this container.
	 */
	public function addText(string $key, array $args = null): static
	{
		$text = GDT_Paragraph::make()->text($key, $args);
		return $this->addField($text);
	}

}
