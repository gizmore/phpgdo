<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A horizontal divider tag.
 *
 * @version 7.0.2
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Divider extends GDT
{

	use WithLabel;

	##############
	### Render ###
	##############
	public function renderCLI(): string
	{
		return sprintf("===%s===\n", $this->renderLabelText());
	}

	public function renderHTML(): string
	{
		$text = $this->renderLabelText();
		$text = $text ? "<h5>{$text}</h5>" : $text;
		return '<div class="gdt-divider">' . $text . '</div>';
	}

//    public function renderCard(): string
//    {
//        $text = $this->renderLabelText();
//        return '<div class="gdt-divider">' . $text . '</div>';
//    }

    /**
	 * Render code block separator.
	 */
	public function renderCodeBlock(): string
	{
		return self::displayCodeBlockS($this->renderLabelText());
	}

	/**
	 * Display a code block separator.
	 */
	public static function displayCodeBlockS(string $title): string
	{
		$len = mb_strlen($title) + 8;
		$row = str_repeat('#', $len) . "\n";
		return sprintf("%s### %s ###\n%1\$s", $row, html($title));
	}

}
