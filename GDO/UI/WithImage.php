<?php
namespace GDO\UI;

/**
 * Adds an image attribute to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
trait WithImage
{
	public GDT_Image $image;
	public function image(GDT_Image $image) : self
	{
		$this->image = $image;
		return $this;
	}
	
}
