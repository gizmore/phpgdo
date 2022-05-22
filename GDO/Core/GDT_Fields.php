<?php
namespace GDO\Core;

/**
 * Like a container but without any rendering.
 * Has fields and input.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
class GDT_Fields extends GDT
{
	use WithName;
	use WithInput;
	use WithFields;
	
}
