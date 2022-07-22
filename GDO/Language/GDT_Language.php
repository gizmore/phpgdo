<?php
namespace GDO\Language;

use GDO\Core\GDT_ObjectSelect;

/**
 * Language select.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class GDT_Language extends GDT_ObjectSelect
{
	public function defaultLabel() : self { return $this->label('language'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->table(GDO_Language::table());
		$this->min = $this->max = 2;
		$this->icon('language');
		$this->cascadeRestrict();
	}
	
	public bool $withName = false;
	public function withName(bool $withName=true)
	{
		$this->withName = $withName;
		return $this;
	}
	
	public bool $withFlag = true;
	public function withFlag(bool $withFlag=true)
	{
		$this->withFlag = $withFlag;
		return $this;
	}
	
	public function plugVars() : array
	{
		return ['en', 'de'];
	}
	
	###############
	### Choices ###
	###############
	private bool $all = false;
	public function all(bool $all=true) : self
	{
		$this->all = $all;
		return $this;
	}
	
	public function initChoices() : self
	{
		return isset($this->choices) ? $this : $this->choices($this->languageChoices());
	}
	
	private function languageChoices() : array
	{
		$languages = GDO_Language::table();
		return $this->all ? $languages->all() : $languages->allSupported();
	}
	
	##################
	### Completion ###
	##################
	public function withCompletion() : self
	{
		return $this->completionHref(href('Language', 'Completion'));
	}

}
