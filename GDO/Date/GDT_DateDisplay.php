<?php
namespace GDO\Date;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithInput;
use GDO\Core\WithName;
use GDO\Core\WithValue;
use GDO\UI\WithPHPJQuery;

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
	public string $emptyTextKey;
	public array $emptyTextArgs;

	#############
	### Empty ###
	#############
	public string $emptyTextRaw = '---';
	public string $dateformat = 'short';

	public function onlyAgo(): self
	{
		$this->showDateAfterSeconds = PHP_INT_MAX;
		return $this;
	}

	public function onlyDate(): self
	{
		$this->showDateAfterSeconds = -1;
		return $this;
	}

	public function emptyText(string $key, array $args = null): self
	{
		$this->emptyTextKey = $key;
		$this->emptyTextArgs = $args;
		return $this;
	}

	public function emptyTextRaw(string $emptyText): self
	{
		unset($this->emptyTextKey);
		unset($this->emptyTextArgs);
		$this->emptyTextRaw = $emptyText;
		return $this;
	}

	##############
	### Format ###
	##############

	public function noEmptyText(): self
	{
		unset($this->emptyTextKey);
		unset($this->emptyTextArgs);
		unset($this->emptyTextRaw);
		return $this;
	}

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
	public function renderCLI(): string
	{
		return $this->renderDateOrAge();
	}

	private function renderDateOrAge(): string
	{
		$date = $this->getVar();
		if (!$date)
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
				Time::displayAge($date),
			]);
		}
		return $display;
	}

	private function renderEmptyText(): string
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

	public function renderHTML(): string
	{
		$display = $this->renderDateOrAge();
        $this->addClass('gdt-age');
		return GDT_Template::php('Date', 'date_html.php', [
			'field' => $this,
			'display' => $display,
		]);
	}

	################
	### Validate ###
	################

	public function plugVars(): array
	{
		return [
			[$this->getName() => '2022-07-18 13:37:42'],
		];
	}

}
