<?php
namespace GDO\Net;

use GDO\Core\GDO_Module;
use GDO\Core\GDO_SEO_URL;
use GDO\Core\GDT_Checkbox;
use GDO\User\GDO_User;

/**
 * Network related stuff.
 *
 * @version 7.0.2
 * @since 6.11.0
 * @author gizmore
 */
final class Module_Net extends GDO_Module
{

	public int $priority = 9;

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/net');
	}

	public function getClasses(): array
	{
		return [
			GDO_Domain::class,
			GDO_SubDomain::class,
		];
	}

	public function onInstall(): void
	{
		if (GDO_SEO_URLS)
		{
			GDO_SEO_URL::addRoute('robots.txt', 'index.php?_mo=Core&_me=Robots');
			GDO_SEO_URL::addRoute('SECURITY.md', 'index.php?_mo=Core&_me=Security');
		}
	}

	public function checkSystemDependencies(): bool
	{
		if (!function_exists('curl_init'))
		{
			return $this->errorSystemDependency('err_php_extension', ['curl']);
		}
		return true;
	}

    public function getConfig(): array
    {
        return [
            GDT_Checkbox::make('record_current_ip')->notNull()->initial('0'),
        ];
    }

    public function getUserConfig(): array
    {
        return [
            GDT_IP::make('last_ip'),
        ];
    }

    public function cfgRecordIP(): bool
    {
        return $this->getConfigValue('record_current_ip');
    }

    public function hookBeforeExecute(): void
    {
        if ($this->cfgRecordIP())
        {
            GDO_User::current()->saveSettingVar('Net', 'last_ip', GDT_IP::current());
        }
    }

}
