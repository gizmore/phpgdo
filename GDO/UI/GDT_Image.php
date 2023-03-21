<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithName;
use GDO\File\GDO_File;

/**
 * HTML Image element.
 *
 * @version 7.0.1
 * @since 6.10.0
 * @author gizmore
 */
class GDT_Image extends GDT
{

	use WithName;
	use WithPHPJQuery;

	public const GIF = 'image/gif';
	public const PNG = 'image/png';
	public const JPG = 'image/jpeg';

	############
	### Vars ###
	############
	public string $src;

	public static function fromFile(GDO_File $file, string $name = null)
	{
		return self::make($name)->src($file->getHref());
	}

	public function src(string $src): self
	{
		$this->src = $src;
		return $this;
	}

	##############
	### Render ###
	##############

	public function htmlSrc(): string
	{
		return isset($this->src) ? ' src="' . html($this->src) . '"' : GDT::EMPTY_STRING;
	}

	###############
	### Factory ###
	###############

	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'image_html.php', ['field' => $this]);
	}

}
