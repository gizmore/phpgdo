<?php
namespace GDO\UI;

/**
 * A textarea is like a GDT_Message without editor.
 *
 * @version 7.0.0
 * @since 6.10.2
 * @author gizmore
 */
class GDT_Textarea extends GDT_Message
{

	##############
	### Editor ###
	##############
	public bool $nowysiwyg = true;

	public function classEditor(): string { return 'as-is'; }

}
