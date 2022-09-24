<?php
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
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
abstract class MethodAjax extends Method
{
	public function isAjax() : bool { return true; }
	public function isIndexed() : bool { return false; }
	public function isLockingSession() { return false; }
	public function isSavingLastUrl() : bool { return false; }
	public function isShownInSitemap() : bool { return false; }

	public function getMethodTitle() : string
	{
		return t('mt_ajax', [$this->getModuleName()]);
	}
	
}
