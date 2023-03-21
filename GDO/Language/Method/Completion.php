<?php
namespace GDO\Language\Method;

use GDO\Core\GDO;
use GDO\Core\GDT_JSON;
use GDO\Core\MethodCompletion;
use GDO\Language\GDO_Language;

/**
 * Complete a GDT_Language.
 *
 * @version 7.0.0
 * @since 6.4.0
 * @author gizmore
 */
final class Completion extends MethodCompletion
{

	protected function gdoTable(): GDO
	{
		# STUB
		return GDO_Language::table();
	}

	public function getMethodTitle(): string
	{
		return 'Language Completion';
	}

	public function getMethodDescription(): string
	{
		return 'Language Completion API for GDOv7';
	}

	public function execute()
	{
		$response = [];
		$q = $this->getSearchTerm();

		$table = GDO_Language::table();
		$languages = isset($_REQUEST['all']) ? $table->all() : $table->allSupported();

		foreach ($languages as $iso => $language)
		{
			if (
				($q === '') || ($language->getISO() === $q) ||
				(mb_stripos($language->renderName(), $q) !== false) ||
				(mb_stripos($language->renderNameIso('en'), $q) !== false)
			)
			{
				$response[] = [
					'id' => $iso,
					'text' => $language->renderName(),
					'display' => $language->renderOption(),
				];
			}
		}

		return GDT_JSON::make()->value($response);
	}

}
