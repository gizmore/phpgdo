<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds an image attribute to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithImage
{
	public GDT $image;
	public function image(GDT $image) : self
	{
		$this->image = $image;
		return $this;
	}
	
}
