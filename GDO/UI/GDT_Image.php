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
class GDT_Image extends GDT
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
	public function src(string $src): static
	{
		$this->src = $src;
		return $this;
	}
	
	public function htmlSrc() : string
	{
		return isset($this->src) ? ' src="'.html($this->src).'"' : GDT::EMPTY_STRING;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'image_html.php', ['field' => $this]);
	}
	
	###############
	### Factory ###
	###############
	public static function fromFile(GDO_File $file, string $name=null)
	{
		return self::make($name)->src($file->getHref());
	}
	
}
