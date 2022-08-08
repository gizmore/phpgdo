<?php
namespace GDO\UI;

/**
 * Adds icon handling to a GDT.
 * The templates have to echo $field->htmlIcon() to render them.
 * 
 * Icons are rendered by the icon provider function stored in GDT_Icon via an icon name and size.
 * Also raw markup can be used instead of an icon name, which is then wrapped in a font-size span.
 * Color only works with markup where css colors could apply, e.g: Fonts or SVG drawings.
 * 
 * @example echo GDT_Icon::iconS('clock', 16, '#f00');
 * @example echo GDT_Icon::make()->rawIcon($site->getIconImage())->iconSize(20)->render();
 * 
 * @see GDT_Icon - for a standalone icon that is a GDT.
 * @see GDT_IconUTF8 - for the minimal icon provider.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
trait WithIcon
{
	###########################
	### Icon-Markup Factory ###
	###########################
	public static function iconS($icon, $iconText=null, $size=null, $color=null) : string
	{
		$style = self::iconStyle($size, $color);
		return call_user_func(GDT_Icon::$iconProvider, $icon, $iconText, $style);
	}
	
	public static function rawIconS($icon, $iconText=null, $size=null, $color=null) : string
	{
		$style = self::iconStyle($size, $color);
		return sprintf('<i class="gdo-icon" title="%s"%s>%s</i>', html($iconText), $style, $icon);
	}
	
	private static function iconStyle($size, $color) : string
	{
		$size = $size === null ? '' : "font-size:{$size}px;";
		$color = $color === null ? '' : "color:$color;";
		return ($color || $size) ? "style=\"$color$size\"" : '';
	}
	
	############
	### Icon ###
	############
	public string $icon;
	public function icon(string $icon=null) : self
	{
		if ($icon)
		{
			$this->icon = $icon;
		}
		else
		{
			unset($this->icon);
		}
		return $this;
	}
	
	public string $iconTextRaw;
	public string $iconTextKey;
	public ?array $iconTextArgs;
	public function iconText(string $textKey, array $textArgs=null) : self
	{
		unset($this->iconTextRaw);
		$this->iconTextKey = $textKey;
		$this->iconTextArgs = $textArgs;
		return $this;
	}
	
	public string $rawIcon;
	public function rawIcon(string $rawIcon) : self
	{
		$this->rawIcon = $rawIcon;
		return $this;
	}

	public ?int $iconSize = null;
	public function iconSize(int $size) : self
	{
		$this->iconSize = $size;
		return $this;
	}

	public ?string $iconColor = null;
	public function iconColor(string $color)
	{
		$this->iconColor = $color;
		return $this;
	}
	
	public function tooltip(string $textKey, array $textArgs=null) : self
	{
		if (!isset($this->icon))
	    {
	        $this->icon('help');
	    }
	    return $this->iconText($textKey, $textArgs);
	}
	
	public function tooltipRaw(string $tooltipText) : self
	{
		$this->iconTextRaw = $tooltipText;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderIconText() : string
	{
		if (isset($this->iconTextKey))
		{
			return t($this->iconTextKey, $this->iconTextArgs);
		}
		if (isset($this->iconTextRaw))
		{
			return $this->iconTextRaw;
		}
		return '';
	}
	
	public function htmlIcon() : string
	{
	    $text = $this->renderIconText();
	    $color = isset($this->iconColor) ? $this->iconColor : ($text ? 'gold' : null);
		if (isset($this->icon))
		{
			return self::iconS($this->icon, $text, $this->iconSize, $color);
		}
		if (isset($this->rawIcon))
		{
			return self::rawIconS($this->rawIcon, $text, $this->iconSize, $color);
		}
		return '';
	}
	
}
