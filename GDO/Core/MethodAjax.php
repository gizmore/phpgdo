<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Raw data retrieval methods.
 *
 * - Do not store last urls.
 * - Does not lock session by default.
 * - Does not show up in sitemap.
 * - Is not indexed by robots.
 * - Do not render the website boilerplate.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
abstract class MethodAjax extends Method
{

	public function isAjax(): bool { return true; }

	public function isIndexed(): bool { return false; }

	public function isSavingLastUrl(): bool { return false; }


//	public function isLocking(): bool { return false; }


	public function isShownInSitemap(): bool { return false; }


	public function getMethodTitle(): string
	{
		return $this->gdoHumanName(); # t('mt_ajax', [$this->getModuleName()]);
	}


}
