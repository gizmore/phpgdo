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
	
	#############
	### Empty ###
	#############
	public string $emptyTextKey;
	public array $emptyTextArgs;
	public string $emptyTextRaw = '---';
	
	public function emptyText(string $key, array $args = null) : self
	{
		$this->emptyTextKey = $key;
		$this->emptyTextArgs = $args;
		return $this;
	}
	
	public function emptyTextRaw(string $emptyText) : self
	{
		unset($this->emptyTextKey);
		unset($this->emptyTextArgs);
		$this->emptyTextRaw = $emptyText;
		return $this;
	}
	
	public function noEmptyText() : self
	{
		unset($this->emptyTextKey);
		unset($this->emptyTextArgs);
		unset($this->emptyTextRaw);
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
	public function renderCLI() : string
	{
		return $this->renderDateOrAge();
	}
	
	public function renderHTML(): string
	{
		$display = $this->renderDateOrAge();
		return GDT_Template::php('Date', 'date_html.php', [
			'field' => $this,
			'display' => $display
		]);
	}

	private function renderDateOrAge() : string
	{
		$date = $this->getVar();
		if ( !$date)
		{
			return $this->renderEmptyText();
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
		return $display;
	}
	
	private function renderEmptyText() : string
	{
		if (isset($this->emptyTextKey))
		{
			return t($this->emptyTextKey, $this->emptyTextArgs);
		}
		if (isset($this->emptyTextRaw))
		{
			return $this->emptyTextRaw;
		}
		return GDT::EMPTY_STRING;
	}
	
	################
	### Validate ###
	################
	public function plugVars() : array
	{
		return [
			[$this->getName() => '2022-07-18 13:37:42'],
		];
	}

}
