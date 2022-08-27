<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Table\GDT_Filter;

/**
 * This GDT does something for/with a proxy GDT.
 * Rendering methods are all directed to the proxy GDT.
 * Used in GDT_Repeat for reapeated arguments, like concat a;b;c;...
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see GDT
 */
trait WithProxy
{
	/**
	 * @var GDT
	 */
	public GDT $proxy;

	public function proxy(GDT $proxy) : self
	{
		$this->proxy = $proxy;
		return $this;
	}
	
	public function getDefaultName() : string { return 'proxy'; }

	public static function makeAs(string $name=null, GDT $proxy) : self
	{
		$obj = parent::make($name);
		$proxy->name($name);
		return $obj->proxy($proxy);
	}
	
	public function isTestable() : bool
	{
		return false;
	}
	
	##############
	### Render ###
	##############
	# various output/rendering formats
	public function render() { return $this->proxy->renderGDT(); }
	public function renderBinary() : string { return $this->proxy->renderBinary(); }
	public function renderCLI() : string { return $this->proxy->renderCLI(); }
	public function renderPDF() : string { return $this->proxy->renderPDF(); }
	public function renderJSON() { return $this->proxy->renderJSON(); }
	public function renderXML() : string { return $this->proxy->renderXML(); }
	# html rendering
	public function renderHTML() : string { return $this->proxy->renderHTML(); }
	public function renderOption() : string { return $this->proxy->renderOption(); }
	public function renderList() : string { return $this->proxy->renderList(); }
	public function renderForm() : string { return $this->proxy->renderForm(); }
	public function renderCard() : string { return $this->proxy->renderCard(); }
	# html table rendering
	public function renderCell() : string { return $this->proxy->renderCell(); }
	public function renderHeader() : string { return $this->proxy->renderHeader(); }
	public function renderFilter(GDT_Filter $f) : string { return $this->proxy->renderFilter($f); }
	public function renderOrder() : string { return $this->proxy->renderOrder(); }

	public function getName() : ?string
	{
		return $this->proxy->getName();
	}
	
	###########################
	### Input / Var / Value ###
	###########################
	public function getVar()
	{
		return $this->proxy->getVar();
	}
	
	public function getValue()
	{
		return $this->proxy->getValue();
	}
	
	public function toVar($value) : ?string
	{
		return $this->proxy->toVar($value);
	}
	
	public function toValue($var=null)
	{
		return $this->proxy->toValue($var);
	}
	
	public function isPositional() : bool
	{
		return $this->proxy->isPositional();
	}
	
}
