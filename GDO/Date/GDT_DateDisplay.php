<?php
namespace GDO\Date;

use GDO\Core\GDT;
use GDO\UI\WithPHPJQuery;
use GDO\Core\GDT_Template;
use GDO\Core\GDO;
use GDO\Core\WithName;
use GDO\Core\WithValue;

/**
 * Display a date either as age or date
 * @author gizmore
 */
final class GDT_DateDisplay extends GDT
{
	use WithName;
	use WithValue;
	use WithPHPJQuery;
	
    public int $showDateAfterSeconds = 172800; # 2 days
    
    public string $dateformat = 'short';
    public function dateformat(string $dateformat) : self
    {
    	$this->dateformat = $dateformat;
    	return $this;
    }
    
    public function renderHTML() : string
    {
        $date = $this->getVar();
        if ($date === null)
        {
        	echo 1;
        	$date = $this->getVar();
        }
        $diff = Time::getDiff($date);
        if ($diff === null)
        {
        	echo 2;
        }
        if ($diff > $this->showDateAfterSeconds)
        {
            $display = Time::displayDate($date, $this->dateformat);
        }
        else
        {
            $display = t('ago', [Time::displayAge($date)]);
        }
        return GDT_Template::php('Date', 'cell/datedisplay.php', ['field' => $this, 'display' => $display]);
    }
    
    public function gdo(GDO $gdo = null) : self
    {
    	$date = $gdo->gdoVar($this->getName());
    	return $this->var($date);
    }

    public function plugVar() : string
    {
    	return '2022-07-19 13:37:42';
    }

}
