<?php
namespace GDO\Install\tpl;
use GDO\Date\Time;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Divider;
use GDO\Form\GDT_Submit;
use GDO\Util\Numeric;
use GDO\Core\Module_Core;
/**
 * @var $form GDT_Form
 */
echo '<';echo '?';echo "php\n";
?>
################################
### GDOv7 Configuration File ###
################################
# if (defined('GDO_CONFIGURED')) return; # double include (ok?)
<?php
$ep = ini_get('error_reporting');
$ep = $ep ?? 'E_ALL';
$de = ini_get('display_errors');
$de = $de ?? 'On'; ?>
error_reporting('<?=$ep?>'); # 
ini_set('display_errors', '<?=$de?>'); # 

/**
 * Please work down each section carefully.
 * Common pitfall is that there are 2 domains to set: GDO_DOMAIN and GDO_SESS_DOMAIN.
 * phpGDOv<?=Module_Core::GDO_REVISION; ?>
 **/
<?php
$created = Time::getDate(microtime(true));
$form->getField('sitecreated')->var($created);

foreach ($form->getAllFields() as $field) :

if ($field instanceof GDT_Divider)
{
	echo "\n";
	echo $field->renderCodeBlock();
}
elseif ($field instanceof GDT_Submit)
{
}
else
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
	
	printf("define('GDO_%s', %s);%s\n", strtoupper($name), $value, $comment);
}
endforeach;
