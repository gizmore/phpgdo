<?php
namespace install;
chdir('../');
use GDO\Core\Application;
use GDO\UI\GDT_Error;
use GDO\Util\Common;
use GDO\Language\Trans;
use GDO\Util\Math;
use GDO\Core\ModuleLoader;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\User\GDO_User;
use GDO\Install\Config;
use GDO\Core\GDT_Response;
@include 'protected/config.php';
require 'GDO7.php';
final class wizard extends Application
{
	private $wizardThemes = ['install', 'default'];
    public function isInstall() : bool
    {
    	return true;
    }
    public function &getThemes() : array
    {
    	return $this->wizardThemes;
    }
}
wizard::instance();
Config::configure();
Logger::init('install');
Debug::init(0, false);
GDO_User::setCurrent(GDO_User::ghost());
$loader = ModuleLoader::instance();
$loader->loadModuleFS('Core');
$install = $loader->loadModuleFS('Install');
Trans::$ISO = GDO_LANGUAGE;
Trans::inited(true);
define('GDO_CORE_STABLE', 1);
try
{
    # Execute Step
    $steps = Config::steps();
    $step = Math::clampInt(Common::getGetInt('step'), 1, count($steps));
    $method = $install->getMethod($steps[$step-1]);
    $result = $method->withAppliedInputs($_REQUEST)->execute();
}
catch (\Throwable $ex)
{
    $result = GDT_Error::make()->exception($ex);
}
$response = GDT_Response::instance()->addField($result);
echo $response->render();
