<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Table\GDT_Filter;
use GDO\Util\Arrays;

/**
 * A select WithObject trait.
 * It behaves a tiny bit different than GDT_Select, for the selected value.
 * It inits the choices with a call to $table->all()!
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
class GDT_ObjectSelect extends GDT_Select
{

	use WithObject;

	public bool $searchable = true;

	public function searchable(bool $searchable): static
	{
		$this->searchable = $searchable;
		return $this;
	}

// 	public function multiple(bool $multiple=true): self
// 	{
// 		return parent::multiple($multiple);
// 	}

	/**
	 * @throws GDO_DBException
	 */
	public function getChoices(): array
	{
		return isset($this->table) ? $this->table->allCached() : GDT::EMPTY_ARRAY;
	}

	public function renderForm(): string
	{
		$this->initChoices();
		if ((isset($this->completionHref)) && (!$this->multiple))
		{
			return GDT_Template::php('Core', 'object_completion_form.php', ['field' => $this]);
		}
		return parent::renderForm();
	}

	public function renderHTML(): string
	{
		if ($obj = $this->getValue())
		{
			if (is_array($obj))
			{
				$back = array_map(function (GDO $gdo)
				{
					return $gdo->renderName();
				}, $obj);
				return Arrays::implodeHuman($back);
			}
			return $obj->renderName();
		}
		return '';
	}

	##############
	### Render ###
	##############

	public function renderJSON(): array|string|null
	{
		/**
		 * @var GDO $value
		 */
		if ($value = $this->getValue())
		{
			if (is_array($value))
			{
				$json = [];
				foreach ($value as $obj)
				{
					$json[] = $obj->toJSON();
				}
				return $json;
			}
			else
			{
				return $value->toJSON();
			}
		}
		return null;
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
// 		$this->initChoices();
		if ($value === null)
		{
			if ($this->notNull)
			{
				if ($this->getVar())
				{
					return $this->errorNotFound();
				}
				return $this->errorNull();
			}
		}
		return true;
	}

//	public function getVar(): string|array|null
//	{
//		return parent::getVar(); # required to overwrite trait.
//	}

	public function errorNotFound(): bool
	{
		return $this->error('err_gdo_not_found', [
			$this->table->gdoHumanName(), html($this->getVar())]);
	}

	#############
	### Value ###
	#############

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'object_filter.php', ['field' => $this, 'f' => $f]);
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		if ($value === null)
		{
			return null;
		}
		return $this->multiple ? $this->multipleToVar($value) : $value->getID();
	}

	/**
	 * @param GDO[] $value
	 */
	public function multipleToVar(array $value): string
	{
		$ids = array_map(function (GDO $gdo)
		{
			return $gdo->getID();
		}, $value);
		return json_encode(array_values($ids));
	}


	public function plugVars(): array
	{
		if (isset($this->table))
		{
			$first = $this->table->select()->first()->exec()->fetchObject();
			if ($first)
			{
				return [
					[$this->name => $first->getID()],
				];
			}
		}
		return GDT::EMPTY_ARRAY;
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $this->multiple ?
			$this->getValueMulti($var) :
			$this->getValueSingle($var);
	}

	public function getValueMulti($var): array
	{
		$back = [];

		if (!is_array($var))
		{
			$var = json_decode($var, true);
		}

		foreach ($var as $id)
		{
			if ($object = $this->table->getById($id))
			{
				$back[$id] = $object;
			}
		}
		return $back;
	}

	public function getValueSingle(?string $id): ?GDO
	{
		return $this->selectToValue($id);
	}

	/**
	 * Try the choices from GDT_Select.
	 * But we are an Object and read from DB!
	 */
	public function selectToValue(?string $var): null|GDO|array
	{
		if ($var !== null)
		{
			if ($value = parent::selectToValue($var))
			{
				return $value;
			}
			return $this->table->getById($var);
		}
		return null;
	}

	##############
	### Config ###
	##############

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'selected' => $this->configJSONSelected(),
		]);
	}

	private function configJSONSelected(): array
	{
		if ($this->multiple)
		{
			$selected = [];
			foreach ($this->getValue() as $value)
			{
				$selected[] = [
					'id' => $value->getID(),
					'text' => $value->renderName(),
					'display' => $value->renderOption(),
				];
			}
		}
		elseif ($value = $this->getValue())
		{
			$selected = [
				'id' => $value->getID(),
				'text' => $value->renderName(),
				'display' => $value->renderOption(),
			];
		}
		else
		{
			$selected = [
				'id' => $this->emptyVar,
				'text' => $this->displayEmptyLabel(),
				'display' => $this->displayEmptyLabel(),
			];
		}
		return $selected;
	}

}
