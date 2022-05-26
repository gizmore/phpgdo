<?php
namespace GDO\Core;

/**
 * A tuple is used as a response value.
 * It inflattens, that means:
 * In HTML it does not get wrapped in a gdt-container.
 * In JSON it does inflatten. instead of response => values you will just get values.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class GDT_Tuple extends GDT
{
	use WithFields;
	
// 	public function renderHTML()
// 	{
// 		$html = '';
// 		foreach ($this->getAllFields() as $gdt)
// 		{
			
// 			$html .= $gdt->renderHTML();
// 		}
// 		return $html;
// 	}
	
}
