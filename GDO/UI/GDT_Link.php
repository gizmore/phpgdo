<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Net\URL;
use GDO\Net\GDT_Url;

/**
 * An anchor for menus or paragraphs.
 * Extends GDT_Url which is a GDT_String configured for URLs.
 * Link renders the HTML anchor.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Link extends GDT_Url
{
	use WithHREF;
	use WithText;
	use WithTarget;

	protected function __construct()
	{
		parent::__construct();
		unset($this->icon); # @TODO: Optionally give a global icon for all links, like TBS did like the enter key.
	}
	
	public static function make(string $name = null) : self
	{
		$obj = self::makeWithLabel($name);
		return $obj->text($name);
	}
	
	###########
	### GDT ###
	###########
	public function isWriteable() : bool { return false; }
	
// 	public function getVar() { return $this->href; }
	
	###########
	### URL ###
	###########
	public function getURL() : URL { return new URL($this->href); }
	
	################
	### Relation ###
	################
	const REL_ALTERNATE = 'alternate';
	const REL_AUTHOR = 'author';
	const REL_BOOKMARK = 'bookmark';
	const REL_EXTERNAL = 'external';
	const REL_HELP = 'help';
	const REL_LICENSE = 'license';
	const REL_NEXT = 'next';
	const REL_NOFOLLOW = 'nofollow';
	const REL_NOREFERRER = 'noreferrer';
	const REL_NOOPENER = 'noopener';
	const REL_PREV = 'prev';
	const REL_SEARCH = 'search';
	const REL_TAG = 'tag';
	
	##############
	### Static ###
	##############
	/**
	 * Output a link / anchor. Not the default GDT behaviour?
	 */
	public static function anchor(string $href, string $textRaw=null) : string
	{
		$textRaw = $textRaw !== null ? $textRaw : $href;
		return self::make()->href($href)->textRaw($textRaw)->render();
	}
	
	public static function anchorMain(): string
	{
		return self::anchor(hrefDefault(), sitename());
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'link_html.php', ['field' => $this]);
	}
	
	public function renderForm() : string
	{
		return $this->renderHTML();
	}

	public function renderCLI() : string
	{
		return $this->renderJSON();
	}
	
	public function renderList() : string
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
