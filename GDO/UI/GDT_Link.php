<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Net\GDT_Url;
use GDO\Net\URL;

/**
 * An anchor for menus or paragraphs.
 * Extends GDT_Url which is a GDT_String configured for URLs.
 * Link renders the HTML anchor.
 *
 * @version 7.0.1
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Link extends GDT_Url
{

	use WithHREF;
	use WithText;
	use WithTarget;

	public const REL_ALTERNATE = 'alternate';
	public const REL_AUTHOR = 'author';

	###########
	### GDT ###
	###########
	public const REL_BOOKMARK = 'bookmark';

// 	public function getVar() { return $this->href; }

	###########
	### URL ###
	###########
	public const REL_EXTERNAL = 'external';

	################
	### Relation ###
	################
	public const REL_HELP = 'help';
	public const REL_LICENSE = 'license';
	public const REL_NEXT = 'next';
	public const REL_NOFOLLOW = 'nofollow';
	public const REL_NOREFERRER = 'noreferrer';
	public const REL_NOOPENER = 'noopener';
	public const REL_PREV = 'prev';
	public const REL_SEARCH = 'search';
	public const REL_TAG = 'tag';

	protected function __construct()
	{
		parent::__construct();
		unset($this->icon); # @TODO: Optionally give a global icon for all links, like TBS did like the enter key.
	}

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

	public static function make(string $name = null): self
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

	public function renderJSON()
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
