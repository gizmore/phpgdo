<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Module config table.
 * As long as nothing is configured, the initial value from the config gdt is used.
 *
 * @TODO Write a configure page that condenses all the module configs into a single page, like settings.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 * @see \GDO\User\GDO_UserSetting
 */
final class GDO_ModuleVar extends GDO
{

	/**
	 * Change a config var.
	 */
	public static function createModuleVar(GDO_Module $module, GDT $gdt): GDT
	{
		foreach ($gdt->getGDOData() as $key => $var)
		{
			self::blank([
				'mv_module' => $module->getID(),
				'mv_name' => $key,
				'mv_value' => $var,
			])->softReplace();
		}
		return $gdt;
	}

	public static function removeModuleVar(GDO_Module $module, GDT $gdt): GDT
	{
		$names = array_keys($gdt->getGDOData());
		$id = $module->getID();
		$in = implode("','", $names);
		self::table()->deleteWhere("mv_module={$id} AND mv_name IN ('{$in}')");
		return $gdt;
	}

	###########
	### GDO ###
	###########

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_Object::make('mv_module')->table(GDO_Module::table())->primary(),
			GDT_Name::make('mv_name')->primary()->unique(false),
			GDT_String::make('mv_value'),
		];
	}

	public function gdoAfterCreate(GDO $gdo): void
	{
		$gdo->reset();
	}

	###########
	### API ###
	###########

	public function gdoAfterUpdate(GDO $gdo): void
	{
		$gdo->reset();
	}

	#############
	### Hooks ###
	#############

	public function gdoAfterDelete(GDO $gdo): void
	{
		$gdo->reset();
	}

}
