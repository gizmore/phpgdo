<?php
namespace GDO\Core;

trait WithParent
{
	public GDT $parent;
	
	public function parent(GDT $parent): static
	{
		$this->parent = $parent;
		return $this;
	}
	
}
