<?php
namespace GDO\Core;

/**
 * Select a method.
 * Optional permission validation.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class GDT_MethodSelect extends GDT_Select
{
	#################
	### Permitted ###
	#################
	public bool $onlyPermitted = false;
	public function onlyPermitted(bool $onlyPermitted = true) : self
	{
		$this->onlyPermitted = $onlyPermitted;
		return $this;
	}
	
	###############
	### Choices ###
	###############
	public function getChoices()
	{
		return [
			'foo' => 'bar',
		];
	}

}
