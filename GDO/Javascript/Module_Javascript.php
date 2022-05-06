<?php
namespace GDO\Javascript;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Enum;
use GDO\File\GDT_Path;
use GDO\UI\GDT_Link;
use GDO\Core\GDT_Checkbox;
use GDO\Javascript\Method\DetectNode;
use GDO\UI\GDT_Divider;

/**
 * Configure Javascript options and binaries.
 *
 * - Provides ".min" appendix if minfication or +concat is enabled.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 */
final class Module_Javascript extends GDO_Module
{
    public int $priority = 10;
    
    public function onLoadLanguage() : void { $this->loadLanguage('lang/js'); }

    public function getConfig() : array
    {
        return [
        	GDT_Divider::make('div_debug'),
        	GDT_Checkbox::make('debug_js')->initial('1'),
        	GDT_Divider::make('div_minify'),
        	GDT_Enum::make('minify_js')->enumValues('no', 'yes', 'concat')->initial('no'),
            GDT_Checkbox::make('compress_js')->initial('0'),
        	GDT_Divider::make('div_binaries'),
            GDT_Link::make('link_node_detect')->href(href('Javascript', 'DetectNode')),
            GDT_Path::make('nodejs_path')->label('nodejs_path'),
            GDT_Path::make('uglifyjs_path')->label('uglifyjs_path'),
            GDT_Path::make('ng_annotate_path')->label('ng_annotate_path'),
        ];
    }
    public function cfgDebug() : string { return $this->getConfigVar('debug_js'); }
    public function cfgMinifyJS() : string { return $this->getConfigVar('minify_js'); }
    public function cfgCompressJS() : string { return $this->getConfigVar('compress_js'); }
    public function cfgNodeJSPath() : string { return $this->getConfigVar('nodejs_path'); }
    public function cfgUglifyPath() : string { return $this->getConfigVar('uglifyjs_path'); }
    public function cfgAnnotatePath() : string { return $this->getConfigVar('ng_annotate_path'); }
    public function cfgMinAppend() : string { return $this->cfgMinifyJS() === 'no' ? '' : '.min'; }
    
    public function onInstall() : void
    {
    	$detect = DetectNode::make();
    	if (!$this->cfgNodeJSPath())
    	{
	    	$detect->detectNodeJS();
    	}
    	if (!$this->cfgAnnotatePath())
    	{
    		$detect->detectAnnotate();
    	}
    	if (!$this->cfgUglifyPath())
    	{
	    	$detect->detectUglify();
    	}
    }
    
    public function onIncludeScripts() : void
    {
    	if ($this->cfgDebug())
    	{
    		$this->addJS('js/gdo-debug.js');
    	}
    }

}
