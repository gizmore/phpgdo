<?php
namespace GDO\Core;

/**
 * Raw data retrieval methods.
 * 
 * - Does not store last url.
 * - Rendering is done without page template.
 * - Does not lock session by default.
 * - Does not show up in sitemap.
 * - Is not indexed by robots.
 * - Does not work in CLI.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
abstract class MethodAjax extends Method
{
    public function isAjax() { return true; }
	public function isSEOIndexed() { return false; }
	public function isLockingSession() { return false; }
	public function saveLastUrl() : bool { return false; }
	public function showInSitemap() : bool { return false; }

}
