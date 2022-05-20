<?php
namespace GDO\Tests;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\User\GDO_User;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;
use GDO\Form\MethodForm;
use GDO\Core\Application;

/**
 * Helper Class to test a gdo method.
 * 
 * @author gizmore
 */
final class MethodTest
{
    # 0 - gizmore (admin)
    # 1 - Peter (staff)
    # 2 - Monica (member)
    # 3 - Gaston (guest)
    # 4 - Sven (staff)
    public static $USERS = []; # store some users here for testing.
    
    public static function make() : self
    {
        return new static();
    }
    
    ###############
    ### Options ###
    ###############
    public $json = false;
    public function json($json=true)
    {
        $this->json = $json;
        return $this;
    }
    
    public $method;
    public function method(Method $method)
    {
        $this->method = $method;
        return $this;
    }
    
    public $parameters = [];
    public function parameters(array $parameters=null)
    {
        if ($parameters)
        {
            $this->parameters = $parameters;
        }
        return $this;
    }
    
    public $getParameters = [];
    public function getParameters(array $getParameters=null)
    {
        if ($getParameters)
        {
            $this->getParameters = $getParameters;
        }
        return $this;
    }
    
    public $user;
    public function user(GDO_User $user)
    {
        $this->user = $user;
        return $this;
    }
    
    ############
    ### Exec ###
    ############
    /**
     * Execute the settings. Copy the parameters into request array. 
     * @param string $btn
     * @return \GDO\Core\GDT_Response
     */
    public function execute($btn='submit')
    {
        # Reset request and response.
//         Application::$RESPONSE_CODE = 200;
//         $_GET = [];
//         $_POST = [];
//         $_REQUEST = [];
        
        # Set user if desired. Default is admin gizmore.
        if ($this->user)
        {
        	GDO_User::setCurrent($this->user);
        }
        
        # Set options
//         $_REQUEST['_fmt'] = $_GET['_fmt'] = $this->json ? 'json' : 'html';
//         $_REQUEST['_ajax'] = $_GET['_ajax'] = $this->json ? '1' : '0';

        $p = array_merge($this->getParameters, $this->parameters);
        $this->method->parameters($p);
//         # Get params
//         foreach ($this->getParameters as $k => $v)
//         {
//             $_REQUEST[$k] = $_GET[$k] = $v;
//         }
        
//         $frm = ($this->method instanceof MethodForm) ? $this->method->formName() : GDT_Form::DEFAULT_NAME;
        
        # Form params
//         $_REQUEST[$frm] = [];
//         $_REQUEST[$frm][$btn] = $btn;
//         foreach ($this->parameters as $key => $value)
//         {
// //             $_POST[$frm][$key] = $value;
//             $_REQUEST[$frm][$key] = $value;
//         }
        
//         $_GET['mo'] = $_REQUEST['mo'] = $this->method->getModuleName();
//         $_GET['me'] = $_REQUEST['me'] = $this->method->getMethodName();
        
        # Exec
        echo "Executing Method {$this->method->gdoHumanName()}\n";
        ob_flush();
        $response = $this->method->exec();
        ob_flush();
        
//         $_REQUEST = []; $_GET = []; $_POST = [];
        
        return $response;
    }
    
    ################################
    ### Automatic Method Testing ###
    ################################
    /**
     * Try to plug default values for a method and test it.
     * @param string $moduleName
     * @param string $methodName
     * @param array $parameters
     * @return self
     */
    public function defaultMethod($moduleName, $methodName, $parameters=[], $button='submit')
    {
        $method = method($moduleName, $methodName);
        
        $getParameters = $parameters;
        
        # Plug default params
        foreach ($method->gdoParameterCache() as $name => $gdt)
        {
            if ($gdt->notNull && $gdt->getVar() === null)
            {
                if (!isset($getParameters[$name]))
                {
                    if ($plugVar = $this->plugParam($gdt, $method))
                    {
                        $getParameters[$name] = $plugVar;
                    }
                }
            }
        }
        
        if ($method instanceof MethodForm)
        {
            foreach ($method->getForm()->getFieldsRec() as $name => $gdt)
            {
                if ($gdt->notNull && $gdt->getVar() === null)
                {
                    if (!isset($parameters[$name]))
                    {
                        if ($plugVar = $this->plugParam($gdt, $method))
                        {
                            $parameters[$name] = $plugVar;
                        }
                    }
                }
            }
        }
        
        # Exec
        return self::make()->method($method)->
            parameters($parameters)->
            getParameters($getParameters)->
            execute($button);
    }
    
    /**
     * Try to guess default params for a GDT.
     * 
     * @param GDT $gdt
     * @return string
     */
    public function plugParam(GDT $gdt, Method $method)
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
