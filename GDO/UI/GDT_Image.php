<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\File\GDO_File;
use GDO\Core\WithName;

/**
 * HTML Image element.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class GDT_Image extends GDT
{
	use WithName;
	use WithPHPJQuery;
	
	const GIF = 'image/gif';
	const PNG = 'image/png';
	const JPG = 'image/jpeg';
	
	############
	### Vars ###
	############
	public string $src;
	public function src(string $src) : self
	{
		$this->src = $src;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
		return GDT_Template::php('UI', 'cell/image.php', ['field' => $this]);
	}
	
	###############
	### Factory ###
	###############
	public static function fromFile(GDO_File $file)
	{
		$image = self::make();
		$image->src($file->getHref());
		return $image;
	}
	
}
