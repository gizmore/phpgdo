<?php
namespace GDO\Core;

/**
 * Module config table.
 * As long as nothing is configured, the initial value from the config gdt is used.
 * @TODO Write a configure page that condenses all the module configs into a single page, like settings.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 * @see \GDO\User\GDO_UserSetting
 */
final class GDO_ModuleVar extends GDO
{
	public function gdoCached() : bool { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
			GDT_Object::make('mv_module')->table(GDO_Module::table())->primary(),
			GDT_Name::make('mv_name')->primary()->unique(false),
			GDT_String::make('mv_value'),
		];
	}
	public function getVarName() : string { return $this->gdoVar('mv_name'); }
	public function getVarValue() : ?string { return $this->gdoVar('mv_value'); }

	###########
	### API ###
	###########
	/**
	 * Change a config var.
	 */
	public static function createModuleVar(GDO_Module $module, GDT $gdt) : GDT
	{
		$inputs = [];
		foreach ($gdt->getGDOData() as $key => $var)
		{
		    self::table()->blank([
    			'mv_module' => $module->getID(),
    		    'mv_name' => $gdt->name,
    			'mv_value' => $var,
    		])->replace();
			$inputs[$key] = $var;
		}
		return $gdt->setGDOData($inputs);
	}
	
// 	public static function removeModuleVar(GDO_Module $module, $varname)
// 	{
// 		$varname = GDO::escapeS($varname);
// 		self::table()->deleteWhere("mv_module={$module->getID()} AND mv_name='$varname'");
// 		return self::table()->blank([
// 		    'mv_module' => $module->getID(),
// 		    'mv_name' => $varname,
// 		    'mv_value' => null,
// 		]);
// 	}
	
}
