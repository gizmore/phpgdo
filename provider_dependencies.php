<?php
/**
 * This prints all non-core-dependencies for a all modules.
 * The list can be copied by authors to Core/ModuleProviders.php
 */
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Core\GDO_Module;

include "GDO7.php";
include "protected/config.php";
Application::instance();
Debug::init();
Database::init(GDO_DB_NAME);
Logger::init('system_provider_dependencies', GDO_ERROR_LEVEL); # 1st init as guest
// GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);

# Bootstrap
Trans::$ISO = GDO_LANGUAGE;

$modules = ModuleLoader::instance()->loadModules(false, true, true);

usort($modules, function(GDO_Module $m1, GDO_Module $m2) {
    return strcasecmp($m1->getName(), $m2->getName());
});

foreach ($modules as $module)
{
    $deps = $module->getDependencies();
    
    if ($deps)
    {
        $deps = '[\'' . implode("', '", $deps) . '\']';
    }
    else
    {
        $deps = '[]';
    }
    
    echo "'" . $module->getName() . "' => " . $deps . ",\n";
}
