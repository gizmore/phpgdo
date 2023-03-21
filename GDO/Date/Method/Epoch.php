<?php
namespace GDO\Date\Method;

use GDO\Core\Application;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\Method;

/**
 * Print the unix timestamp, and other formats. Unused?
 *
 * @version 6.10.6
 * @since 6.10.4
 * @author gizmore
 */
final class Epoch extends Method
{

	public function getMethodDescription(): string
	{
		return $this->getMethodTitle();
	}

	public function gdoParameters(): array
	{
		return [
			GDT_EnumNoI18n::make('format')->enumValues('unix', 'java', 'micro')->notNull()->initial('unix'),
		];
	}

	public function execute()
	{
		$format = $this->gdoParameterVar('format');
		switch ($format)
		{
			case 'unix':
				$time = Application::$TIME;
				break;
			case 'java':
				$time = round(Application::$MICROTIME * 1000.0);
				break;
			case 'micro':
				$time = Application::$MICROTIME;
				break;
		}
		$key = 'msg_time_' . $format;
		return $this->message($key, [$time]);
	}

}
