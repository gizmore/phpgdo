<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Card;

/**
 * A list item.
 * Has a title, subtitle, subtext, image and menu.
 *
 * @version 7.0.3
 * @since 6.7.0
 * @author gizmore
 * @see GDT_Card
 */
final class GDT_ListItem extends GDT_Card
{

	public GDT $right;

	public function right(GDT $content): self
	{
		$this->right = $content;
		return $this;
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('Table', 'list_item.php', ['gdt' => $this]);
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		$data = [];
		if ($this->hasTitle())
		{
			$data['title'] = $this->renderTitle();
		}
		if ($this->hasSubTitle())
		{
			$data['subtitle'] = $this->renderSubTitle();
		}
		if (isset($this->content))
		{
			$data['content'] = $this->content->renderHTML();
		}
		if (isset($this->right))
		{
			$data['right'] = $this->right->renderHTML();
		}
		return $data;
	}

}
