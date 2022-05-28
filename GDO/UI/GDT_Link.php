<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;
use GDO\Net\URL;

/**
 * An anchor for menus or paragraphs.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Link extends GDT_String
{
	use WithIcon;
	use WithLabel;
	use WithHREF;
	use WithPHPJQuery;
	use WithAnchorRelation;

	public bool $caseSensitive = true;
	
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
	
	################
	### GDO href ###
	################
// 	public function gdo(GDO $gdo=null)
// 	{
// 	    if ($gdo)
// 	    {
//     	    $method = "href_{$this->name}";
//     	    if (method_exists($gdo, $method))
//     	    {
//     	        $this->href(call_user_func([$gdo, $method]));
//     	    }
// 	    }
// 	    return parent::gdo($gdo);
// 	}
	
	
	/**
	 * Output a link / anchor.
	 * @deprecated not the default GDT behaviour. Yet ok? NO!
	 * @param string $href
	 * @param string $label
	 * @return string
	 */
	public static function anchor($href, $label=null)
	{
		$label = $label !== null ? $label : $href;
		return self::make()->href($href)->labelRaw($label)->render();
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string { return $this->renderHTML(); }
	public function renderHTML() : string { return GDT_Template::php('UI', 'link_html.php', ['link' => $this]); }
	public function renderJSON() { return $this->renderLabel() . ': ' . " ( {$this->href} )"; }
	public function renderFilter($f) : string { return GDT_String::make($this->name)->renderFilter($f); }
	
	###################
	### Link target ###
	###################
	public $target;
	public function target($target) { $this->target = $target; return $this; }
	public function targetBlank() { return $this->target('_blank'); }
	public function htmlTarget() { return $this->target === null ? '' : " target=\"{$this->target}\""; }

	###########
	### URL ###
	###########
	public function getURL() { return new URL($this->href); }
	
}
