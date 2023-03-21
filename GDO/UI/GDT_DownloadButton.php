<?php
namespace GDO\UI;

/**
 * A download button with label and icon.
 * Adds gdt-download class.
 *
 * @version 7.0.0
 * @since 6.10.1
 * @author gizmore
 */
final class GDT_DownloadButton extends GDT_Button
{

	protected function __construct()
	{
		parent::__construct();
		$this->name = 'download';
		$this->icon('download');
		$this->addClass('gdt-download-button');
	}

	public function defaultLabel(): self { return $this->label('btn_download'); }

}
