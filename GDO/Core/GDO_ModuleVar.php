<?php
namespace GDO\Core;

/**
 * Module config table.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
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
			GDT_Object::make('mv_module_id')->table(GDO_Module::table())->primary(),
			GDT_Name::make('mv_name')->primary()->unique(false),
			GDT_String::make('mv_value'),
		];
	}
	public function getVarName() { return $this->gdoVar('mv_name'); }
	public function getVarValue() { return $this->gdoVar('mv_value'); }
	
	public static function createModuleVar(GDO_Module $module, GDT $gdt)
	{
	    $var = $gdt->getVar();
	    if ($var === null)
		{
		    $gdt->var($gdt->initial);
		    $moduleVar = self::removeModuleVar($module, $gdt->name);
		}
		else
		{
		    $moduleVar = self::table()->blank([
    			'mv_module_id' => $module->getID(),
    		    'mv_name' => $gdt->name,
    			'mv_value' => $var,
    		])->replace();
		}
		
		return $moduleVar;
	}
	
	public static function removeModuleVar(GDO_Module $module, $varname)
	{
		$varname = GDO::escapeS($varname);
		self::table()->deleteWhere("mv_module_id={$module->getID()} AND mv_name='$varname'");
		return self::table()->blank([
		    'mv_module_id' => $module->getID(),
		    'mv_name' => $varname,
		    'mv_value' => null,
		]);
	}
	
}
