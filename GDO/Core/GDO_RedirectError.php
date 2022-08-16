<?php
namespace GDO\Core;

use GDO\UI\GDT_Redirect;
use GDO\UI\WithHREF;

final class GDO_RedirectError extends \Exception
{
	use WithHREF;
	
	public function __construct(string $key, ?array $args, string $href, int $code = GDO_Error::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
		$this->href = $href;
		echo GDT_Redirect::make()->href($href)->text($key, $args)->render();
	}

}
