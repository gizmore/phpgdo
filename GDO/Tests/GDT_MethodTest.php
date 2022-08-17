<?php
namespace GDO\Tests;

use GDO\User\GDO_User;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;
use GDO\Core\GDT_Method;

/**
 * Helper Class to test GDOv7 methods.
 * Holds global user objects for test cases.
 * This is ensured by a quirky and important module priority and dependency graph.
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
    
    ###########
    ### boo ###
    ###########
    /**
     * @deprecated Use ->inputs() method
     */
    public function parameters(array $inputs) : self
    {
    	return $this->inputs($inputs);
    }
    
    ############
    ### Exec ###
    ############
    public function execute(string $button=null)
    {
    	if ($button === null)
    	{
    		$this->clibutton();
    	}
    	else
    	{
    		$this->addInput($button, '1');
    	}
    	return parent::execute();
    }
    
//     /**
//      * Try to guess default params for a GDT.
//      * 
//      * @param GDT $gdt
//      * @return string
//      */
//     public function xxxplugParam(GDT $gdt, Method $method) : string
//     {
//         $klass = get_class($gdt);
//         $plugvar = $gdt->plugVar();
//         if ($plugvar)
//         {
//             echo "Try to auto plug {$method->getModuleName()}::{$method->getMethodName()}.{$gdt->name} which is a {$klass} with {$plugvar}\n";
//             ob_flush();
//         }
//         else
//         {
//             echo Color::red("FAILED")." to auto plug {$method->getModuleName()}::{$method->getMethodName()}.{$gdt->name} which is a {$klass} with {$plugvar}\n";
//             ob_flush();
//         }
        
//         $this->addInput($gdt->getName(), $plugvar);
        
//         return $plugvar;
//     }

}
