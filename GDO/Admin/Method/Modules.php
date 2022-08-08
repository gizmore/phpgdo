<?php
namespace GDO\Admin\Method;

use GDO\Admin\GDT_ModuleVersionFS;
use GDO\Admin\MethodAdmin;
use GDO\Core\GDO_Module;
use GDO\DB\ArrayResult;
use GDO\Table\MethodTable;
use GDO\Core\GDO;
use GDO\Core\GDT_Checkbox;
use GDO\Core\ModuleLoader;
use GDO\Admin\GDT_ModuleNameLink;
use GDO\Admin\GDT_ModuleAdminButton;
use GDO\Core\GDT_Version;
use GDO\Core\GDT_UInt;

/**
 * Overview of all modules in FS and DB.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.2
 */
class Modules extends MethodTable
{
	use MethodAdmin;
	
	/**
	 * @var GDO_Module[]
	 */
	private array $modules;
	
	public function getMethodTitle() : string { return t('btn_modules'); }
	
	public function gdoTable() : GDO { return GDO_Module::table(); }
	
	public function useFetchInto() : bool { return false; }
	
	public function isPaginated() { return false; }
	
	public function getDefaultOrder() : ?string { return 'module_name ASC'; }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function onInit()
	{
		$this->modules = ModuleLoader::instance()->loadModules(false, true, false);
		return parent::onInit();
	}
	
	public function execute()
	{
		unset($this->modules['install']);
		return parent::execute();
	}
	
	public function getResult() : ArrayResult
	{
	    return new ArrayResult($this->modules, $this->gdoFetchAs());
	}
	
	public function gdoHeaders() : array
	{
		return [
			GDT_UInt::make('module_priority')->unsigned()->label('priority'),
			GDT_Checkbox::make('module_enabled')->label('enabled'),
			GDT_Version::make('module_version')->label('version_db'),
			GDT_ModuleVersionFS::make('module_version_fs')->label('version_fs'),
			GDT_ModuleNameLink::make('module_name')->label('name'),
			GDT_ModuleAdminButton::make('administrate_module')->label('btn_admin'),
		];
	}
	
}
