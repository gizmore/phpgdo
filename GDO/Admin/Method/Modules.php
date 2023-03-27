<?php
declare(strict_types=1);
namespace GDO\Admin\Method;

use GDO\Admin\GDT_ModuleAdminButton;
use GDO\Admin\GDT_ModuleNameLink;
use GDO\Admin\GDT_ModuleVersionFS;
use GDO\Admin\MethodAdmin;
use GDO\Core\GDO;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Version;
use GDO\Core\ModuleLoader;
use GDO\DB\ArrayResult;
use GDO\Table\MethodTable;
use GDO\User\GDO_Permission;

/**
 * Overview of all modules in FS and DB.
 *
 * @version 7.0.3
 * @since 3.0.2
 * @author gizmore
 */
class Modules extends MethodTable
{

	use MethodAdmin;

	/**
	 * @var GDO_Module[]
	 */
	private array $modules;

	public function getMethodTitle(): string { return t('btn_modules'); }

	public function gdoTable(): GDO { return GDO_Module::table(); }

	public function useFetchInto(): bool { return false; }

	public function isPaginated(): bool { return false; }

	public function getDefaultOrder(): ?string { return 'module_name ASC'; }

	public function onMethodInit(): ?GDT
	{
		$this->modules = ModuleLoader::instance()->loadModules(false, true);
		return parent::onMethodInit();
	}

	public function execute(): GDT
	{
		unset($this->modules['install']);
		return parent::execute();
	}

	public function getResult(): ArrayResult
	{
		return new ArrayResult($this->modules, $this->gdoFetchAs());
	}

	public function gdoHeaders(): array
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

	public function getPermission(): ?string { return GDO_Permission::STAFF; }

}
