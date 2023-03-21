<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\Form\WithActions;

/**
 * A dialog.
 * Very simple JS is used to display it.
 * Should almost work with CSS only.
 *
 * @version 6.11.0
 * @since 6.10.4
 * @author gizmore
 */
class GDT_Dialog extends GDT
{

	use WithTitle;
	use WithFields;
	use WithPHPJQuery;
	use WithActions;

	public $opened = false;

	##############
	### Opened ###
	##############
	/**
	 * Start dialog in modal mode?
	 *
	 * @var bool
	 */
	public $modal = false;

	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'cell/dialog.php', ['field' => $this]);
	}

	#############
	### Modal ###
	#############

	/**
	 * Start dialog in open mode?
	 *
	 * @param bool $opened
	 *
	 * @return GDT_Dialog
	 */
	public function opened($opened = true)
	{
		$this->opened = $opened;
		return $this;
	}

	public function modal($modal = true)
	{
		$this->modal = $modal;
		return $this;
	}

	public function okButton($key = 'btn_ok', array $args = null)
	{
		$btn = GDT_Button::make('ok')->label($key, $args);
		$btn->attr('onclick', "GDO.closeDialog('{$this->id()}', 'ok')");
		$this->actions()->addField($btn);
		return $this;
	}

	public function cancelButton($key = 'btn_cancel', array $args = null)
	{
		$btn = GDT_Button::make('cancel')->label($key, $args);
		$btn->attr('onclick', "GDO.closeDialog('{$this->id()}', 'cancel')");
		$this->actions()->addField($btn);
		return $this;
	}

}
