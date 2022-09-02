<?php
namespace GDO\UI;

/**
 * A tooltip is a help icon with hover text.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.2.0
 */
class GDT_Tooltip extends GDT_Icon
{
	public string $icon = 'help';

	public function renderHTML() : string
	{
		return $this->htmlIcon();
// 		return GDT_Template::php('UI', 'cell/tooltip.php', ['field'=>$this]);
	}
	
	public function renderCard() : string
	{
		return $this->displayCard("<i>{$this->renderIconText()}</i>");
	}

	public function displayCard($var) : string
	{
		return sprintf("<label>%s:</label><span>%s</span>\n",
			$this->renderLabel(), $var);
	}

}
