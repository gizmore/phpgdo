<?php
namespace GDO\UI;

/**
 * Add HTML href capabilities.
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
 */
trait WithHREF
{

	public string $href;

	public function href(string $href = null): self
	{
		if ($href)
		{
			$this->href = $href;
		}
		else
		{
			unset($this->href);
		}
		return $this;
	}

	public function htmlHREF(): string
	{
		return isset($this->href) ? sprintf(' href="%s"', html($this->href)) : '';
	}

	####################
	### Replace HREF ###
	####################
	/**
	 * Get this href with a replaced parameter.
	 */
	public function replacedHREF(string $key, ?string $var): string
	{
		return isset($this->href) ?
			self::replacedHREFS($this->href, $key, $var) :
			'';
	}

	/**
	 * Replace a GET Parameter inside an URL.
	 * Adds it, if not found.
	 * Removes if replacement var is empty.
	 *
	 * @TODO Speed up replacedHREFS() or at least fix the ?problem for the first element.
	 */
	public static function replacedHREFS(string $href, string $key, ?string $var): string
	{
		$new = $var ? ("&{$key}=" . urlencode($var)) : '';
		if (strpos($href, "&{$key}=") !== false)
		{
			$key = preg_quote($key);
			$href = preg_replace("#&{$key}=[^&]+#D", $new, $href);
		}
		else
		{
			$href .= $new;
		}
		return $href;
	}

}
