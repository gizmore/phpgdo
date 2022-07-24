<?php
namespace GDO\Tests;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\User\GDO_User;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;
use GDO\Core\GDT_Method;
use GDO\UI\Color;

/**
 * Helper Class to test GDOv7 methods.
 * 
 * @TODO: Replace Tests\MethodTest with GDT_Method or GDT_Expression. Copy the test user stuff! Add effective user attribute to the GDT_Method.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.11.2
 */
final class GDT_MethodTest extends GDT_Method
{
    # 0 - gizmore (admin)
    # 1 - Peter (staff)
    # 2 - Monica (member)
    # 3 - Gaston (guest)
    # 4 - Sven (staff)
    /**
     * @var GDO_User[]
     */
    public static array $TEST_USERS = []; # store some users here for testing.
    
    /**
     * @deprecated Use ->inputs() method
     */
    public function parameters(array $inputs) : self
    {
    	return $this->inputs($inputs);
    }
    
    public function execute()
    {
    	$this->clibutton();
    	return parent::execute();
    }
    
    /**
     * Try to guess default params for a GDT.
     * 
     * @param GDT $gdt
     * @return string
     */
    public function xxxplugParam(GDT $gdt, Method $method) : string
    {
        $klass = get_class($gdt);
        $plugvar = $gdt->plugVar();
        if ($plugvar)
        {
            echo "Try to auto plug {$method->getModuleName()}::{$method->getMethodName()}.{$gdt->name} which is a {$klass} with {$plugvar}\n";
            ob_flush();
        }
        else
        {
            echo Color::red("FAILED")." to auto plug {$method->getModuleName()}::{$method->getMethodName()}.{$gdt->name} which is a {$klass} with {$plugvar}\n";
            ob_flush();
        }
        
        $this->addInput($gdt->getName(), $plugvar);
        
        return $plugvar;
    }

}
