<?php
namespace GDO\Core;

/**
 * Like a container but without any rendering.
 * Has fields and input.
 *
 * @version 7.0.0
 * @since 7.0.0
 * @author gizmore
 */
class GDT_Fields extends GDT
{

	use WithName;
	use WithInput;
	use WithFields;
}
