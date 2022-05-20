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
	#####################
	### Link relation ###
	#####################
    public string $relation;
	public function relation(string $relation) : self
	{
		$this->relation = $relation ? trim($this->relation . " $relation") : $this->relation;
		return $this;
	}

	public function htmlRelation() : string
	{
		return isset($this->relation) ? " rel=\"$this->relation\"" : '';
	}

	public function noFollow() : self
	{
		return $this->relation(GDT_Link::REL_NOFOLLOW);
	}
	
}
