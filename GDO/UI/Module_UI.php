<?php
namespace GDO\UI;

use GDO\Core\GDO_Module;

/**
 * The UI module offers many html rendering widgets and traits.
 * 
 * Not limited to:
 * - Icon rendering
 * - Message and editor rendering
 * - HTML rendering widgets like pre,p,hr,h1,etc
 * - WithPHPJQuery for a jquery like PHP api. (rudimentary)
 * - Buttons and Links
 * - Color utility
 * - CLI vs HTML utility
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
final class Module_UI extends GDO_Module
{
    public int $priority = 20;
    public string $license = 'LGPL2.1';
    
    public function isCoreModule() : bool
    {
    	return true;
    }

    public function thirdPartyFolders() : array
    {
    	return ['/htmlpurifier/'];
    }
    
    public function getLicenseFilenames() : array
    {
    	return [
    		'htmlpurifier/LICENSE',
    	];
    }
    
//     public function getUserSettings() : array
//     {
//     	return [
// //     		GDT_MessageEditor::make('editor'),
//     	];
//     }
    
}
