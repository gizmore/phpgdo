<?php
namespace GDO\Language;

use GDO\Core\GDT_ObjectSelect;
use GDO\User\GDO_User;

/**
 * Language select. Defaults to only allow supported.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Language extends GDT_ObjectSelect
{

	public bool $withName = false;
	public bool $withFlag = true;

	###############
	### Options ###
	###############
	private bool $all = false;

	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_Language::table());
		$this->min = $this->max = 2;
		$this->icon('language');
		$this->cascadeRestrict();
	}

	public function gdtDefaultLabel(): ?string
    { return 'language'; }

	public function plugVars(): array
	{
		$name = $this->getName();
		return [
			[$name => 'en'],
			[$name => 'de'],
		];
	}

	###############
	### Current ###
	###############

	protected function getChoices(): array
	{
		$languages = GDO_Language::table();
        return $this->all ? $languages->all() : $languages->allSupported();
	}

	############
	### Test ###
	############

	public function all(bool $all = true): self
	{
		$this->all = $all;
		return $this;
	}

	###############
	### Choices ###
	###############

	public function withName(bool $withName = true)
	{
		$this->withName = $withName;
		return $this;
	}

	public function withFlag(bool $withFlag = true)
	{
		$this->withFlag = $withFlag;
		return $this;
	}

	public function initialCurrent(bool $bool = true): self
	{
		return $this->initial(GDO_User::current()->getLangISO());
	}

	##################
	### Completion ###
	##################

	public function withCompletion(): self
	{
		return $this->completionHref(href('Language', 'Completion'));
	}

}
