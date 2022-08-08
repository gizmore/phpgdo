<?php
namespace GDO\Table;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;
use GDO\Core\Application;

/**
 * The table module allows some default settings for table responses an
 * 
 * - add list for <ul> stuff
 * - add table for <table> stuff
 * - 
 * - configure default items per page
 * - configure default suggestions per request. 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.3
 * @see GDT_List
 * @see GDT_Table
 * @see MethodTable
 * @see MethodQueryTable
 */
final class Module_Table extends GDO_Module
{
	public int $priority = 10;
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
		    GDT_UInt::make('spr')->initial('20')->min(1)->max(100),
		    GDT_UInt::make('ipp_cli')->initial('10')->min(1)->max(1000),
		    GDT_UInt::make('ipp_http')->initial('20')->min(1)->max(1000),
		];
	}
	public function cfgSuggestionsPerRequest() : int { return $this->getConfigValue('spr'); }
	public function cfgItemsPerPageCLI() : int { return $this->getConfigValue('ipp_cli'); }
	public function cfgItemsPerPageHTTP() : int { return $this->getConfigValue('ipp_http'); }
	public function cfgItemsPerPage() : int
	{
	    return Application::$INSTANCE->isCLI() ?
	       $this->cfgItemsPerPageCLI() :
	       $this->cfgItemsPerPageHTTP();
	}
	
}
