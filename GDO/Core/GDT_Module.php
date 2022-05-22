<?php
namespace GDO\Core;

/**
 * A module select.
 * Features installed and uninstalled choices.
 * Loads module via module loader.
 * PlugVar for auto tests is module Core.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 * 
 * @see GDO_Module
 */
final class GDT_Module extends GDT_ObjectSelect
{
    protected function __construct()
    {
        parent::__construct();
        $this->table(GDO_Module::table());
    }
    
//     public function toVar($value) : ?string
//     {
//         if ($value)
//         {
//             return strtolower($value->getName());
//         }
//     }
    
    ####################
    ### Un/Installed ###
    ####################
    public bool $installed = true;
    public function installed(bool $installed=true)
    {
    	$this->installed = $installed;
    	return $this;
    }
    
    public bool $uninstalled = false;
    public function uninstalled(bool $uninstalled=true)
    {
    	$this->uninstalled = $uninstalled;
    	return $this;
    }
    
    ###############
    ### Choices ###
    ###############
    public function initChoices()
    {
        if (!$this->choices)
        {
            $this->choices = [];
            
            $modules = ModuleLoader::instance()->loadModules($this->installed, $this->uninstalled);
            
            foreach ($modules as $module)
            {
                if ( (($module->isInstalled()) && $this->installed) ||
                     ((!$module->isInstalled()) && $this->uninstalled) )
                {
                    $this->choices[$module->getLowerName()] = $module->renderName();
                }
            }
        }
    }
    
    #################
    ### Var/Value ###
    #################
    public function plugVar() : string
    {
        return 'Core';
    }
    
    public function getValueSingle(string $moduleName) : ?GDO_Module
    {
        return ModuleLoader::instance()->getModule($moduleName, true, false);
    }
    
    public function getValueMulti(string $var) : array
    {
        $loader = ModuleLoader::instance();
        $back = [];
        foreach (json_decode($var) as $id)
        {
            if ($object = $loader->getModule($id))
            {
                $back[$id] = $object;
            }
        }
        return $back;
    }
    
}
