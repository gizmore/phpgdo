<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Object is an integer in the database. Uses WithObject trait for magic.
 *
 * @version 7.0.3
 * @since 6.4.0
 * @author gizmore
 * @see WithObject
 */
class GDT_Object extends GDT_UInt
{

	use WithObject;
	use WithCompletion;

	final public const MAX_SUGGESTIONS = 15;


	public bool $searchable = true;

	public function htmlClass(): string
	{
		return ' gdt-object';
	}

	public function searchable(bool $searchable): static
	{
		$this->searchable = $searchable;
		return $this;
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		if ($obj = $this->getValue())
		{
			return $obj->renderHTML();
		}
		if (null !== ($var = $this->getVar()))
		{
			return $var;
		}
		return GDT::EMPTY_STRING;
	}

	public function renderOption(): string
	{
		/** @var GDO $obj * */
		if ($obj = $this->getValue())
		{
			return $obj->renderOption();
		}
		return GDT::EMPTY_STRING;
	}

	public function renderForm(): string
	{
		if (isset($this->completionHref))
		{
			return GDT_Template::php('Core', 'object_completion_form.php', ['field' => $this]);
		}
		else
		{
			return GDT_Template::php('Core', 'object_form.php', ['field' => $this]);
		}
	}

	##############
	### Filter ###
	##############
// 	public function filterVar($rq=null) : null
// 	{
// // 		return $this->_getRequestVar("{$rq}[f]", null, $this->filterField ? $this->filterField : $this->name);
// 	}

	##############
	### Config ###
	##############
	public function configJSON(): array
	{
		if ($gdo = $this->getValue())
		{
			$selected = [
				'id' => $gdo->getID(),
				'text' => $gdo->renderName(),
				'display' => json_quote($gdo->renderOption()),
			];
		}
		else
		{
			$ph = $this->renderPlaceholder();
			$selected = [
				'id' => null,
				'text' => $ph,
				'display' => $ph,
			];
		}
		return array_merge(parent::configJSON(), [
			'cascade' => $this->cascade,
			'selected' => $selected,
			'completionHref' => $this->completionHref ?? null,
		]);
	}

}
