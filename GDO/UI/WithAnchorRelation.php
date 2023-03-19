<?php
namespace GDO\UI;

/**
 * Adds anchor relation to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 * @see GDT_Link
 * @see GDT_Button
 */
trait WithAnchorRelation
{
    public string $relation;
    
	public function relation(string $relation): static
	{
		if (isset($this->relation))
		{
			$this->relation .= " $relation";
		}
		else
		{
			$this->relation = $relation;
		}
		return $this;
	}

	public function htmlRelation() : string
	{
		return isset($this->relation) ? " rel=\"{$this->relation}\"" : '';
	}

	public function noFollow(): static
	{
		return $this->relation(GDT_Link::REL_NOFOLLOW);
	}
	
}
