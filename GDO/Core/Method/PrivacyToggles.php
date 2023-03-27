<?php
namespace GDO\Core\Method;

use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Tuple;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Install\Config;
use GDO\Language\Trans;
use GDO\UI\GDT_Accordeon;
use GDO\UI\GDT_Card;

/**
 * Show information about privacy related settings.
 *
 * @author gizmore
 *
 */
final class PrivacyToggles extends Method
{

	public function isTrivial(): bool
	{
		# This method messes with setting fields instead!!!
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('privacy_settings');
	}

	public function execute(): GDT
	{
		ModuleLoader::instance()->loadModuleFS('Install')->onLoadLanguage();
		Trans::inited();

		$result = GDT_Tuple::make('result');
		$panel = GDT_Response::make('information');
		$result->addFields($panel);
		$result->addFields($this->getCoreAccordeon());
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			if ($fields = $module->getPrivacyRelatedFields())
			{
				$result->addFields($this->createAccordeon($module, $fields));
			}
		}
		return $result;
	}

	private function getCoreAccordeon(): GDT_Accordeon
	{
		$wanted = [
			'force_ssl',
			'log_request',
			'sess_https',
			'sess_samesite',
		];
		$fields = [];
		foreach (Config::fields() as $gdt)
		{
			if (in_array($gdt->getName(), $wanted, true))
			{
				$fields[] = $gdt;
			}
		}
		return $this->createAccordeonB($fields)->title('t_privacy_core_toggles');
	}

	private function createAccordeonB(array $fields): GDT_Accordeon
	{
		$acc = GDT_Accordeon::make();
		$card = GDT_Card::make();
		foreach ($fields as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$cfgkey = 'cfg_' . $name;
				if (Trans::hasKey($cfgkey))
				{
					$gdt->label($cfgkey);
					$cfgkey = 'tt_' . $cfgkey;
					if (Trans::hasKey($cfgkey))
					{
						$gdt->tooltip($cfgkey);
					}
				}
				$card->addFields($gdt);
			}
		}
		return $acc->addFields($card);
	}

	private function createAccordeon(GDO_Module $module, array $fields): GDT_Accordeon
	{
		$acc = $this->createAccordeonB($fields);
		return $acc->titleRaw($module->gdoHumanName());
	}

}
