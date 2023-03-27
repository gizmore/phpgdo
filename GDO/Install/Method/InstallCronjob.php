<?php
namespace GDO\Install\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\UI\GDT_Container;

/**
 * Show info howto install cronjob
 *
 * @author gizmore
 */
final class InstallCronjob extends Method
{

	public function isUserRequired(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('install_title_5');
	}

	public function getMethodDescription(): string
	{
		return $this->getMethodTitle();
	}

	public function execute(): GDT
	{
		Database::init();
		$hasdb = GDO_DB_HOST !== null;
		ModuleLoader::instance()->loadModules($hasdb, !$hasdb);
		return $this->renderPage();
	}

	public function renderPage(): GDT
	{
		$container = GDT_Container::make()->vertical();
		GDT_Hook::callHook('InstallCronjob', $container);
		return $this->templatePHP('page/installcronjob.php', ['container' => $container]);
	}

}
