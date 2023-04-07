<?php
declare(strict_types=1);
namespace GDO\CLI;

use GDO\Form\MethodForm;
use GDO\User\GDO_Permission;

/**
 * Abstract CLI method does not work via HTTP.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
abstract class MethodCLI extends MethodForm
{

	public function isCLI(): bool { return true; }


}
