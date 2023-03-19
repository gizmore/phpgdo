<?php
namespace GDO\UI;

/**
 * Flex class handling trait for containers.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.3.0
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
	public function flex(int $direction = self::HORIZONTAL, bool $wrap = true, bool $shrink = false): static
	{
		$this->flex = true;
		$this->flexDirection = $direction;
		$this->flexWrap = $wrap;
		$this->flexShrink = $shrink;
		return $this;
	}

	public function noflex(): static
	{
		$this->flex = false;
		return $this;
	}

	public function horizontal(bool $wrap = true, bool $shrink = false): static
	{
		$this->flex = true;
		$this->flexDirection = self::HORIZONTAL;
		$this->flexWrap = $wrap;
		return $this->shrink($shrink);
	}

	public function vertical(bool $wrap = false, bool $shrink = false): static
	{
		$this->flex = true;
		$this->flexDirection = self::VERTICAL;
		$this->flexWrap = $wrap;
		return $this->shrink($shrink);
	}

	public function wrap(bool $wrap = true): static
	{
		$this->flexWrap = $wrap;
		return $this;
	}

	public function grow(bool $grow = true): static
	{
		return $this->shrink( !$grow);
	}

	public function shrink(bool $shrink = true): static
	{
		$this->flexShrink = $shrink;
		return $this;
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
