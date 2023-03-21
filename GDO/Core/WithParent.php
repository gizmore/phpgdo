<?php
namespace GDO\Core;

trait WithParent
{

	public GDT $parent;

	public function parent(GDT $parent): self
	{
		$this->parent = $parent;
		return $this;
	}

}
