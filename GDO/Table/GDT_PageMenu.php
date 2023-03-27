<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\DB\ArrayResult;
use GDO\DB\Query;
use GDO\UI\GDT_Link;
use GDO\UI\WithHREF;
use GDO\Util\Math;

/**
 * Pagemenu widget.
 *
 * For parameters @version 7.0.0
 *
 * @since 3.1.0
 * @see GDT_PageNum and @see GDT_IPP.
 *
 * @author gizmore
 */
class GDT_PageMenu extends GDT
{

	use WithHREF;

	public string $pageName;
	public int $numItems = 0;
	public int $ipp = 10;
	public int $page;

	###########
	### IPP ###
	###########
	public int $shown = 5;

	public function pageName(string $pageName): self
	{
		$this->pageName = $pageName;
		return $this;
	}

// 	public function getIPPField() : GDT_IPP
// 	{
// 		if (!isset($this->ipp))
// 		{
// 			$this->ipp = GDT_IPP::make();
// 		}
// 		return $this->ipp;
// 	}

	################
	### Page Num ###
	################

	public function numItems(int $numItems): self
	{
		$this->numItems = $numItems;
		return $this;
	}

	public function ipp(int $ipp): self
	{
		$this->ipp = $ipp;
		return $this;
	}

// 	public function getPageNumField() : GDT_PageNum
// 	{
// 		if (!isset($this->pageNum))
// 		{
// 			$this->pageNum = GDT_PageNum::make();
// 		}
// 		return $this->pageNum;
// 	}

	public function page(int $page): self
	{
		$this->page = $page;
		return $this;
	}

	public function shown(int $shown): self
	{
		$this->shown = $shown;
		return $this;
	}

	/**
	 * Set num items via query.
	 *
	 * @deprecated because the query is called twice then.
	 */
	public function query(Query $query): self
	{
		$this->numItems = $query->copy()->selectOnly('COUNT(*)')->exec()->fetchValue();
		return $this;
	}

	public function paginateQuery(Query $query): self
	{
		$query->limit($this->ipp, $this->getFrom());
		return $this;
	}

	public function getFrom(): int
	{
		return self::getFromS($this->getPage(), $this->ipp);
	}

	public static function getFromS(int $page, int $ipp): int
	{
		return ($page - 1) * $ipp;
	}

	/**
	 * @return int
	 */
	public function getPage(): int
	{
		$min = 1;
		$max = $this->getPageCount();
		return Math::clampInt($this->page, $min, $max);
	}

	public function getPageCount(): int
	{
		return self::getPageCountS($this->numItems, $this->ipp);
	}

	public static function getPageCountS(int $numItems, int $ipp): int
	{
		return max([intval((($numItems - 1) / $ipp) + 1), 1]);
	}

	public function indexToPage(int $index): int
	{
		return self::indexToPageS($index, $this->ipp);
	}

	public static function indexToPageS(int $index, int $ipp): int
	{
		return intval($index / $ipp) + 1;
	}

	public function paginateResult(ArrayResult $result, $page, $ipp): ArrayResult
	{
		$data = array_slice($result->getData(), self::getFromS($page, $ipp), $ipp);
		return $result->data($data);
	}

	##############
	### Render ###
	##############
	public function isTestable(): bool
	{
		return false;
	}

	public function renderCLI(): string
	{
		return t('pagemenu_cli', [$this->getPage(), $this->getPageCount()]);
	}

	/**
	 * Get anchor relation for a page. Either next, prev or nofollow.
	 *
	 * @param PageMenuItem $page
	 *
	 * @return string
	 * @see GDT_Link
	 */
	public function relationForPage(PageMenuItem $page)
	{
		$current = $this->getPage();
		if (!is_numeric($page->page))
		{
			return GDT_Link::REL_NOFOLLOW;
		}
		elseif (($page->page - 1) == $current)
		{
			return GDT_Link::REL_NEXT;
		}
		elseif (($page->page + 1) == $current)
		{
			return GDT_Link::REL_PREV;
		}
		else
		{
			return GDT_Link::REL_NOFOLLOW;
		}
	}

	public function renderJSON(): array|string|null
	{
		return [
			'href' => isset($this->href) ? $this->href : null,
			'items' => isset($this->numItems) ? $this->numItems : null,
			'ipp' => isset($this->ipp) ? $this->ipp : null,
			'page' => isset($this->pageNum) ? $this->getPage() : null,
			'pages' => (int)$this->getPageCount(),
		];
	}

	public function renderHTML(): string
	{
		if ($this->getPageCount() > 1)
		{
			$tVars = [
				'pagemenu' => $this,
				'pages' => $this->pagesObject(),
			];
			return GDT_Template::php('Table', 'pagemenu_html.php', $tVars);
		}
		return '';
	}

	public function configJSON(): array
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
			$page = $curr + $i;
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
		$name = $this->pageName;
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


}
