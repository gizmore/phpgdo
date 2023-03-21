<?php
namespace GDO\UI;

/**
 * Add image size attributes to a GDT.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
trait WithImageSize
{

	public int $imageWidth = 42;
	public int $imageHeight = 42;

	public function imageSize(int $w, int $h = 0): self
	{
		$this->imageWidth = $w;
		$this->imageHeight = $h ? $h : $w;
		return $this;
	}

}
