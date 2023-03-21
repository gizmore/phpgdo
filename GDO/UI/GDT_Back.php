<?php
namespace GDO\UI;

/**
 * The back button points to your origin.
 * It has a default icon and label.
 *
 * @version 6.11.2
 * @since 6.3.0
 * @author gizmore
 */
final class GDT_Back extends GDT_Link
{

	protected function __construct()
	{
		parent::__construct();
		$this->name('back');
		$this->icon('back');
		$this->href(GDT_Redirect::hrefBack());
	}

	public function defaultLabel(): self { return $this->label('btn_back'); }

	public function htmlClass(): string
	{
		return ' gdt-link gdt-back';
	}

}
