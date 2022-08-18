<?php
namespace GDO\Date;

use GDO\Core\GDT;
use GDO\UI\WithPHPJQuery;
use GDO\Core\GDT_Template;
use GDO\Core\WithName;
use GDO\Core\WithValue;
use GDO\Core\WithInput;

/**
 * Display a date either as age or date
 *
 * @author gizmore
 */
final class GDT_DateDisplay extends GDT
{
	use WithName;
	use WithValue;
	use WithInput;
	use WithPHPJQuery;

	#####################
	### Render Switch ###
	#####################
	public int $showDateAfterSeconds = 172800;
	public function onlyAgo() : self
	{
		$this->showDateAfterSeconds = PHP_INT_MAX;
		return $this;
	}
	
	public function onlyDate() : self
	{
		$this->showDateAfterSeconds = -1;
		return $this;
	}
	
	##############
	### Format ###
	##############
	public string $dateformat = 'short';
	public function dateformat(string $dateformat): self
	{
		$this->dateformat = $dateformat;
		return $this;
	}

	# ##########
	# ## Now ###
	# ##########
	public function initialNow(): self
	{
		return $this->initial(Time::getDate());
	}

	# #############
	# ## Render ###
	# #############
	public function renderHTML(): string
	{
		$date = $this->getVar();
		if ( !$date)
		{
			return '---';
		}
		$diff = Time::getDiff($date);
		if ($diff > $this->showDateAfterSeconds)
		{
			$display = Time::displayDate($date, $this->dateformat);
		}
		else
		{
			$display = t('ago', [
				Time::displayAge($date)
			]);
		}
		return GDT_Template::php('Date', 'date_html.php', [
			'field' => $this,
			'display' => $display
		]);
	}

	public function plugVars() : array
	{
		return [
			[$this->getName() => '2022-07-18 13:37:42'],
		];
	}

}
