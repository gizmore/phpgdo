<?php
namespace GDO\Table;

use GDO\DB\Query;
use GDO\Core\GDT;
use GDO\Util\Math;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;
use GDO\UI\WithLabel;
use GDO\UI\GDT_Link;
use GDO\Core\GDT_Fields;
use GDO\DB\ArrayResult;

/**
 * Pagemenu widget.
 * @author gizmore
 * @version 7.0.0
 * @since 3.1.0
 */
class GDT_PageMenu extends GDT
{
	use WithHREF;
	use WithLabel;
	
	public int $numItems = 0;
	public function numItems(int $numItems) : self
	{
		$this->numItems = $numItems;
		return $this;
	}
	
	public GDT_IPP $ipp;
	public function getIPPField() : GDT_IPP
	{
		if (!isset($this->ipp))
		{
			$this->ipp = GDT_IPP::make();
		}
		return $this->ipp;
	}
	
	public function ipp(int $ipp) : self
	{
	    return $this;
	}
	
	
	public GDT_PageNum $pageNum;
	
	public function getPageNumField() : GDT_PageNum
	{
		if (!isset($this->pageNum))
		{
			$this->pageNum = GDT_PageNum::make();
		}
		return $this->pageNum;
	}
	
	public function page(int $page) : self
	{
		$this->getPageNumField()->value($page);
		return $this;
	}
	
	public $shown = 5;
	public function shown($shown)
	{
	    $this->shown = $shown;
	    return $this;
	}
	
	/**
	 * @var GDT_Fields
	 */
	public $headers;
	public function headers(GDT_Fields $headers)
	{
	    $this->headers = $headers;
	    return $this;
	}
	
	/**
	 * Set num items via query.
	 * @optional
	 * @param Query $query
	 * @return self
	 */
	public function query(Query $query)
	{
		$this->numItems = $query->copy()->selectOnly('COUNT(*)')->exec()->fetchValue();
		return $this;
	}
	
	public function getPageCount()
	{
		return self::getPageCountS($this->numItems, $this->getIPPField()->getVar());
	}
	
	public static function getPageCountS($numItems, $ipp)
	{
		return max(array(intval((($numItems-1) / $ipp)+1), 1));
	}
	
	public function filterQuery(Query $query, $rq=null) : self
	{
		$query->limit($this->ipp->getVar(), $this->getFrom());
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPage() : int
	{
		$page = (int)$this->getPageNumField()->getValue();
		$min = 1;
		$max = $this->getPageCount();
		return Math::clampInt($page, $min, $max);
	}
	
	public function getFrom()
	{
		return self::getFromS($this->getPage(), $this->ipp->getVar());
	}
	
	public static function getFromS($page, $ipp)
	{
		return ($page - 1) * $ipp;
	}
	
	public function indexToPage($index)
	{
		return self::indexToPageS($index, $this->ipp);
	}
	
	public static function indexToPageS($index, $ipp)
	{
		return intval($index / $ipp) + 1;
	}
	
	public function paginateResult(ArrayResult $result, $page, $ipp)
	{
	    $data = array_slice($result->getData(), self::getFromS($page, $ipp), $ipp);
	    return $result->data($data);
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		return t('pagemenu_cli', [$this->getPage(), $this->getPageCount()]);
	}
	
	public function renderJSON()
	{
		return [
			'href' => isset($this->href) ? $this->href : null,
			'items' => isset($this->numItems) ? $this->numItems : null,
			'ipp' => isset($this->ipp) ? $this->ipp : null,
			'page' => isset($this->pageNum) ? $this->getPage() : null,
			'pages' => (int)$this->getPageCount(),
		];
	}
	
	public function renderHTML() : string
	{
		if ($this->getPageCount() > 1)
		{
			$tVars = [
				'pagemenu' => $this,
				'pages' => $this->pagesObject(),
			];
			return GDT_Template::php('Table', 'cell/pagemenu.php', $tVars);
		}
		return '';
	}
	
	public function configJSON() : array
	{
	    return array_merge($this->renderJSON(), parent::configJSON());
	}
	
	#############
	### Items ###
	#############
	private function pagesObject()
	{
		$curr = $this->getPage();
		$nPages = $this->getPageCount();
		$pages = [];
		$pages[] = new PageMenuItem($curr, $this->replaceHREF($curr), true);
		for ($i = 1; $i <= $this->shown; $i++)
		{
			$page = $curr - $i;
			if ($page > 0)
			{
				array_unshift($pages, new PageMenuItem($page, $this->replaceHREF($page)));
			}
			$page = $curr+ $i;
			if ($page <= $nPages)
			{
				$pages[] = new PageMenuItem($page, $this->replaceHREF($page));
			}
		}
		
		if (($curr - $this->shown) > 1)
		{
			array_unshift($pages, PageMenuItem::dotted());
			array_unshift($pages, new PageMenuItem(1, $this->replaceHREF(1)));
		}

		if (($curr + $this->shown) < $nPages)
		{
			$pages[] = PageMenuItem::dotted();
			$pages[] = new PageMenuItem($nPages, $this->replaceHREF($nPages));
		}
		
		return $pages;
	}
	
	private function replaceHREF($page)
	{
		$name = $this->getPageNumField()->name;
		if (strpos($this->href, $name) === false)
		{
			$this->href .= strpos($this->href, '?') ? '&' : '?';
			$this->href .= $name;
			$this->href .= '=';
			$this->href .= $page;
		}
		else
		{
			$this->href = preg_replace("/{$name}=\d+/iD", "{$name}={$page}", $this->href);
		}
		return $this->href;
	}
	
	/**
	 * Get anchor relation for a page. Either next, prev or nofollow.
	 * @see GDT_Link
	 * @param PageMenuItem $page
	 * @return string
	 */
	public function relationForPage(PageMenuItem $page)
	{
		$current = $this->getPage();
		if (!is_numeric($page->page))
		{
		    return GDT_Link::REL_NOFOLLOW;
		}
		elseif ( ($page->page - 1) == $current)
		{
			return GDT_Link::REL_NEXT;
		}
		elseif ( ($page->page + 1) == $current)
		{
			return GDT_Link::REL_PREV;
		}
		else
		{
			return GDT_Link::REL_NOFOLLOW;
		}
	}

}
