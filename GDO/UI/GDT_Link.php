<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Net\GDT_Url;
use GDO\Net\URL;

/**
 * An anchor for menus or paragraphs.
 * Extends GDT_Url which is a GDT_String configured for URLs.
 * Link renders the HTML anchor.
 *
 * @TODO Make GDT_Link inherit from GDT instead of GDT_Url
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Link extends GDT_Url
{

	use WithHREF;
	use WithText;
	use WithTarget;

	final public const REL_ALTERNATE = 'alternate';
	final public const REL_AUTHOR = 'author';
	final public const REL_BOOKMARK = 'bookmark';

	###########
	### GDT ###
	###########
	final public const REL_EXTERNAL = 'external';

	###########
	### URL ###
	###########
	final public const REL_HELP = 'help';

	################
	### Relation ###
	################
	final public const REL_LICENSE = 'license';
	final public const REL_NEXT = 'next';
	final public const REL_NOFOLLOW = 'nofollow';
	final public const REL_NOREFERRER = 'noreferrer';
	final public const REL_NOOPENER = 'noopener';
	final public const REL_PREV = 'prev';
	final public const REL_SEARCH = 'search';
	final public const REL_TAG = 'tag';

	public bool $searchable = false;


	public static function anchorMain(): string
	{
		return self::anchor(hrefDefault(), sitename());
	}

	/**
	 * Output a link / anchor. Not the default GDT behaviour?
	 */
	public static function anchor(string $href, string $textRaw = null): string
	{
		$textRaw = $textRaw !== null ? $textRaw : $href;
		return self::make()->href($href)->textRaw($textRaw)->render();
	}

	public static function make(string $name = null): static
	{
		$obj = self::makeWithLabel($name);
		return $obj->text($name);
	}

	##############
	### Static ###
	##############

	public function isWriteable(): bool { return false; }

	public function getURL(): URL { return new URL($this->href); }

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'link_html.php', ['field' => $this]);
	}

	public function renderForm(): string
	{
		return $this->renderHTML();
	}

	public function renderCLI(): string
	{
		return $this->renderJSON();
	}

	public function renderList(): string
	{
		$html = $this->renderHTML();
		$card = $this->displayCard($html);
		return "<span>$card</span>\n";
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		$out = '';
		if ($l = $this->renderLabelText())
		{
			$out .= $l;
			$out .= ': ';
		}
		if (isset($this->href))
		{
			$out .= "( {$this->href} )";
		}
		return $out;
	}

}
