<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\Core\Application;

/**
 * A panel that un/collapses on click to the title.
 * Content is inherited via container.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class GDT_Accordeon extends GDT_Container
{
	use WithTitle;

	# #############
	# ## Opened ###
	# #############
	public bool $opened = false;

	public function opened(bool $opened = true): self
	{
		$this->opened = $opened;
		return $this;
	}

	public function closed(bool $closed = true): self
	{
		return $this->opened( !$closed);
	}

	# #############
	# ## Render ###
	# #############
	public function renderHTML(): string
	{
		return $this->renderAccordeon(GDT::RENDER_CELL);
	}

	public function renderForm(): string
	{
		return $this->renderAccordeon(GDT::RENDER_FORM);
	}
	
	public function renderCard() : string
	{
		$old = Application::$MODE;
		Application::$MODE = GDT::RENDER_CARD;
		return $this->renderAccordeon(GDT::RENDER_CARD);
		Application::$MODE = $old;
	}
	
	protected function renderAccordeon(int $mode): string
	{
		return GDT_Template::php('UI', 'accordeon_html.php', [
			'field' => $this,
			'mode' => $mode,
		]);
	}

}
