<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;
use GDO\Net\URL;
use GDO\Net\GDT_Url;

/**
 * An anchor for menus or paragraphs.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Link extends GDT_Url
{
	use WithHREF;
	use WithTarget;

	protected function __construct()
	{
		parent::__construct();
		unset($this->icon);
		$this->caseS();
	}
	
	###########
	### GDT ###
	###########
	public function isWriteable() : bool { return false; }
	
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
	
	/**
	 * Output a link / anchor.
	 * @deprecated not the default GDT behaviour. Yet ok? NO?
	 */
	public static function anchor(string $href, string $labelRaw=null) : string
	{
		$labelRaw = $labelRaw !== null ? $labelRaw : $href;
		return self::make()->href($href)->labelRaw($labelRaw)->renderCell();
	}
	
	##############
	### Render ###
	##############
	public function renderForm() : string { return $this->renderHTML(); }
	public function renderCell() : string { return $this->renderHTML(); }
	public function renderHTML() : string { return GDT_Template::php('UI', 'link_html.php', ['link' => $this]); }
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
	public function renderFilter($f) : string { return GDT_String::make($this->name)->renderFilter($f); }
	
	###########
	### URL ###
	###########
	public function getURL() : URL { return new URL($this->href); }
	
}
