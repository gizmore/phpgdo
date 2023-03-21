<?php
namespace GDO\UI;

/**
 * Adds anchor relation to a GDT.
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
 * @see GDT_Link
 * @see GDT_Button
 */
trait WithAnchorRelation
{

	public string $relation;

	public function htmlRelation(): string
	{
		return isset($this->relation) ? " rel=\"{$this->relation}\"" : '';
	}

	public function noFollow(): self
	{
		return $this->relation(GDT_Link::REL_NOFOLLOW);
	}

	public function relation(string $relation): self
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

}
