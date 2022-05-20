<?php
namespace GDO\Tests;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\User\GDO_User;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Method;

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
    public static array $USERS = []; # store some users here for testing.
    
//     ############
//     ### Exec ###
//     ############
//     /**
//      * Execute the settings. Copy the parameters into request array. 
//      * @param string $btn
//      * @return \GDO\Core\GDT_Response
//      */
//     public function execute(string $btn='submit', bool $permissions=true) : GDT
//     {
//     	if (isset($this->user))
//         {
//         	GDO_User::setCurrent($this->user);
//         }

//         $p = array_merge($this->getParameters, $this->parameters);
//         $p[$btn] = '1';
//         $this->method->parameters($p);

//         echo "Executing Method {$this->method->gdoHumanName()}\n";
//         ob_flush();
//         $response = $permissions ? $this->method->exec() : $this->method->executeWithInit();
//         ob_flush();
//         return $response;
//     }
    
    ################################
    ### Automatic Method Testing ###
    ################################
    /**
     * Try to plug default values for a method and test it.
     * 
     * @TODO: Keep defaultMethod() in MethodTest?
     * 
     * @param string $moduleName
     * @param string $methodName
     * @param array $parameters
     * @return self
     */
    public function defaultMethod(Method $method)
    {
    	$inputs = [];
    	$method = GDT_Method::make()->method($method)->runAs();
    	$method->inputs($inputs);
    	
    	
    	
    	
    	
    	
    	
    	
//         $getParameters = $parameters;
        
//         # Plug default params
//         foreach ($method->gdoParameterCache() as $name => $gdt)
//         {
//             if ($gdt->notNull && $gdt->getVar() === null)
//             {
//                 if (!isset($getParameters[$name]))
//                 {
//                     if ($plugVar = $this->plugParam($gdt, $method))
//                     {
//                         $getParameters[$name] = $plugVar;
//                     }
//                 }
//             }
//         }
        
//         if ($method instanceof MethodForm)
//         {
//             foreach ($method->getForm()->getFieldsRec() as $name => $gdt)
//             {
//                 if ($gdt->notNull && $gdt->getVar() === null)
//                 {
//                     if (!isset($parameters[$name]))
//                     {
//                         if ($plugVar = $this->plugParam($gdt, $method))
//                         {
//                             $parameters[$name] = $plugVar;
//                         }
//                     }
//                 }
//             }
//         }
        
//         # Exec
//         $gdt_method = self::make()->method($method)->runAs();
        
        
//         $gdt_method->execute()
//             parameters($parameters)->
//             getParameters($getParameters)->
//             execute($button);
    }
    
    /**
     * Try to guess default params for a GDT.
     * 
     * @param GDT $gdt
     * @return string
     */
    public function plugParam(GDT $gdt, Method $method) : string
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
            echo "FAILED to auto plug {$method->getModuleName()}::{$method->getMethodName()}.{$gdt->name} which is a {$klass} with {$plugvar}\n";
            ob_flush();
        }
        return $plugvar;
    }

}
