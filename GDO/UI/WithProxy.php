<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_Field;
use GDO\Table\GDT_Filter;

/**
 * This GDT does something for/with a proxy GDT.
 * Rendering methods are all directed to the proxy GDT.
 * Used in GDT_Repeat for reapeated arguments, like concat a;b;c;...
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 */
trait WithProxy
{

	public GDT_Field $proxy;

	public static function makeAs(string $name, GDT_Field $proxy): static
	{
		$obj = self::make($name);
		$proxy->name($name);
		return $obj->proxy($proxy);
	}

	public function proxy(GDT_Field $proxy): static
	{
		$this->proxy = $proxy;
		return $this;
	}

//	public function getDefaultName(): string { return 'proxy'; }

	public function isTestable(): bool
	{
		return false;
	}

	##############
	### Render ###
	##############
	# various output/rendering formats
	public function render(): array|string|null { return $this->proxy->renderGDT(); }

	public function renderBinary(): string { return $this->proxy->renderBinary(); }

	public function renderCLI(): string { return $this->proxy->renderCLI(); }

	public function renderPDF(): string { return $this->proxy->renderPDF(); }

	public function renderJSON(): array|string|null { return $this->proxy->renderJSON(); }

	public function renderXML(): string { return $this->proxy->renderXML(); }

	# html rendering
	public function renderHTML(): string { return $this->proxy->renderHTML(); }

	public function renderOption(): string { return $this->proxy->renderOption(); }

	public function renderList(): string { return $this->proxy->renderList(); }

	public function renderForm(): string { return $this->proxy->renderForm(); }

	public function renderCard(): string { return $this->proxy->renderCard(); }

	# html table rendering
	public function renderCell(): string { return $this->proxy->renderCell(); }

	public function renderFilter(GDT_Filter $f): string { return $this->proxy->renderFilter($f); }

	public function getName(): ?string
	{
		return $this->proxy->getName();
	}

	public function getVar(): string|array|null
	{
		return $this->proxy->getVar();
	}

	public function getValue(): bool|int|float|string|array|null|object
	{
		return $this->proxy->getValue();
	}

	###########################
	### Input / Var / Value ###
	###########################

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $this->proxy->toVar($value);
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $this->proxy->toValue($var);
	}

	public function isPositional(): bool
	{
		return $this->proxy->isPositional();
	}

	public function renderHeader(): string { return $this->proxy->renderHeader(); }

	public function renderOrder(): string { return $this->proxy->renderOrder(); }

}
