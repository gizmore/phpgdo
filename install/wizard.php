<?php
namespace install;

define('GDO_TIME_START', microtime(true));
chdir('../');

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Install\Config;
use GDO\Language\Trans;
use GDO\UI\GDT_Error;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Util\Math;
use Throwable;

@include 'protected/config.php';
require 'GDO7.php';

final class wizard extends Application
{

	private $wizardThemes = ['install', 'default'];

	public function isInstall(): bool
	{
		return true;
	}

//	public function &getThemes(): array
//	{
//		return $this->wizardThemes;
//	}

}

wizard::instance()->modeDetected(GDT::RENDER_WEBSITE);
Config::configure();
Logger::init('install');
Debug::init(0, false);
GDO_User::setCurrent(GDO_User::ghost());
$loader = ModuleLoader::instance();
$loader->loadModuleFS('Core', false);
$install = $loader->loadModuleFS('Install', false);
Trans::$ISO = GDO_LANGUAGE;
$loader->initModules(false);
$loader->loadLangFiles();
Trans::inited(true);
define('GDO_CORE_STABLE', 1);
global $me;
/** @var Method $me * */
try
{
	# Execute Step
	$steps = Config::steps();
	$step = Math::clampInt(Common::getGetInt('step'), 1, count($steps));
	$me = $install->getMethod($steps[$step - 1]);
	$result = $me->appliedInputs($_REQUEST)->execute();
}
catch (Throwable $ex)
{
	$result = GDT_Error::fromException($ex);
}
$response = GDT_Response::make()->addField($result);
echo $response->renderWebsite();
