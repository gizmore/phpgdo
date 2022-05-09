<?php
namespace GDO\Admin\Method;

use GDO\Admin\GDT_ModuleVersionFS;
use GDO\Admin\MethodAdmin;
use GDO\Core\GDO_Module;
use GDO\DB\ArrayResult;
use GDO\Table\MethodTable;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Decimal;
use GDO\Core\ModuleLoader;
use GDO\Table\GDT_Sort;
use GDO\Admin\GDT_ModuleNameLink;
use GDO\Admin\GDT_ModuleAdminButton;

/**
 * Overview of all modules in FS and DB.
 * @author gizmore
 * @version 6.11.0
 * @since 3.0.2
 */
class Modules extends MethodTable
{
	use MethodAdmin;
	
	/**
	 * @var GDO_Module[]
	 */
	private $modules;
	
	public function getTitleLangKey() { return 'btn_modules'; }
	
	public function gdoTable() { return GDO_Module::table(); }
	
	public function useFetchInto() { return false; }
	
	public function isPaginated() { return false; }
	
	public function getDefaultOrder() { return 'module_name ASC'; }
	
	public function getPermission() { return 'staff'; }
	
	public function execute() : GDT
	{
		$this->modules = ModuleLoader::instance()->loadModules(false, true);
		return parent::execute();
	}
	
	public function getResult()
	{
	    return new ArrayResult($this->modules, $this->gdoTable());
	}
	
	/**
	 * @override
	 */
	public function gdoHeaders()
	{
		return [
			GDT_Sort::make('module_sort'),
// 			GDT_Int::make('module_priority')->unsigned()->label('priority'),
			GDT_Checkbox::make('module_enabled')->label('enabled'),
			GDT_Decimal::make('module_version')->digits(1, 2)->label('version_db'),
			GDT_ModuleVersionFS::make('module_version_fs')->label('version_fs'),
			GDT_ModuleNameLink::make('module_name')->label('name'),
			GDT_ModuleAdminButton::make('administrate_module')->label('btn_admin'),
		];
	}
	
}
