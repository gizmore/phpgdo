<?phpnamespace GDO\Date\tpl;use GDO\Date\GDT_DateDisplay;use GDO\Date\Time;/** @var $field GDT_DateDisplay * *//** @var $display string * */?><span<?=$field->htmlAttributes()?> data-timestamp="<?=Time::getTimestamp($field->getVar());?>"><?=$display?></span>