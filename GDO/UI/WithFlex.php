<?php
namespace GDO\UI;

/**
 * Flex class handling trait for containers.
 *
 * @version 7.0.1
 * @since 6.3.0
 * @author gizmore
 * @see GDT_Bar
 * @see GDT_Box
 * @see GDT_Container
 */
trait WithFlex
{

	public bool $flex = false;

	public int $flexDirection = self::HORIZONTAL;

	public bool $flexWrap = false;

	public bool $flexShrink = false;

	/**
	 * Enable flex for this container.
	 */
	public function flex(int $direction = self::HORIZONTAL, bool $wrap = true, bool $shrink = false): self
	{
		$this->flex = true;
		$this->flexDirection = $direction;
		$this->flexWrap = $wrap;
		$this->flexShrink = $shrink;
		return $this;
	}

	public function noflex(): self
	{
		$this->flex = false;
		return $this;
	}

	public function horizontal(bool $wrap = true, bool $shrink = false): self
	{
		$this->flex = true;
		$this->flexDirection = self::HORIZONTAL;
		$this->flexWrap = $wrap;
		return $this->shrink($shrink);
	}

	public function shrink(bool $shrink = true): self
	{
		$this->flexShrink = $shrink;
		return $this;
	}

	public function vertical(bool $wrap = false, bool $shrink = false): self
	{
		$this->flex = true;
		$this->flexDirection = self::VERTICAL;
		$this->flexWrap = $wrap;
		return $this->shrink($shrink);
	}

	public function wrap(bool $wrap = true): self
	{
		$this->flexWrap = $wrap;
		return $this;
	}

	public function grow(bool $grow = true): self
	{
		return $this->shrink(!$grow);
	}

	# #############
	# ## Render ###
	# #############

	/**
	 * Render classname for flex classes.
	 */
	private function flexClass(): string
	{
		return $this->flexDirection === self::HORIZONTAL ? 'flx-row' : 'flx-col';
	}

}
