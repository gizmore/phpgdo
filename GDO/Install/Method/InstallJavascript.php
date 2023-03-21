<?php
namespace GDO\Install\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;

/**
 * Show info howto install bower components.
 *
 * @author gizmore
 */
final class InstallJavascript extends Method
{

	public function isUserRequired(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('install_title_7');
	}

	public function getMethodDescription(): string
	{
		return $this->getMethodTitle();
	}

	public function execute()
	{
		Database::init();
		ModuleLoader::instance()->loadModulesA();
		return $this->renderPage();
	}

	public function renderPage(): GDT
	{
		return $this->templatePHP('page/installjavascript.php');
	}

}
