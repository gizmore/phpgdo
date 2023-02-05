<?php
namespace GDO\Install\tpl;
use GDO\Date\Time;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Divider;
use GDO\Form\GDT_Submit;
use GDO\Util\Numeric;
use GDO\Core\Module_Core;
use GDO\Install\Config;
/**
 * @var $form GDT_Form
 */
echo '<';echo '?';echo "php\n"; # it's a php script
?>
####################
### GDOv7 Config ###
####################
# if (defined('GDO_CONFIGURED')) return; # double include (needed?)
<?php
$ep = ini_get('error_reporting');
$ep = $ep ?? 'E_ALL';
$de = ini_get('display_errors');
$de = $de ?? 'On'; ?>
error_reporting('<?=$ep?>'); # Should be not less than E_All & ~E_DEPRECATED & ~E_STRICT.
ini_set('display_errors', '<?=$de?>'); # Should be enabled / does not matter because of \GDO\Core\Debug. 

/**
 * Please work down each section carefully.
 *
 * Common pitfalls:
 *
 * - The config rewrites itself upon gdo_update.sh!
 * - 
 * - There are 2 domain settings: GDO_DOMAIN and GDO_SESS_DOMAIN.
 * - GDO_DB_ENABLED is easily overlooked.
 * 
 * (c)2021-2023 - gizmore@wechall.net
 * re-created by GDOv<?=Module_Core::GDO_REVISION; ?> on <?=Time::displayDate()?>.
 **/
<?php
$created = Time::getDate(microtime(true));
$form->getField('sitecreated')->var($created);

foreach ($form->getAllFields() as $field) :

if ($field instanceof GDT_Divider)
{
	echo "\n" . $field->renderCodeBlock();
}
elseif (!($field instanceof GDT_Submit))
{
    $name = $field->name;
    
	$value = $field->getValue();
	if (is_string($value))
	{
		if ($name === 'chmod')
		{
			$value = $value; # do nothing
		}
		elseif ($name === 'error_level')
		{
		    $value = "0x".Numeric::baseConvert($value, 10, 16);
		}
		else
		{
			$value = "'$value'";
		}
	}
	elseif ($value === null)
	{
		$value = 'null';
	}
	elseif (is_array($value))
	{
		$value = implode(',', $value);
		$value = "'$value'";
	}
	elseif (is_bool($value))
	{
		$value = $value ? 'true' : 'false';
	}
	
	$comment = $field->renderIconText();
	$comment = $comment ? " # {$comment}" : '';
	
	printf("define('%s', %s);%s\n",
		Config::getConstantName(), $value, $comment);
}
endforeach;
