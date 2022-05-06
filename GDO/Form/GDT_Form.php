<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\WithText;

final class GDT_Form extends GDT
{
	use WithText;
	use WithTitle;
	use WithFields;
	use WithActions;
	
}
