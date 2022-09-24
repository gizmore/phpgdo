<?php
namespace GDO\CLI;

use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Core\GDT;
use GDO\Core\GDO_Module;
use GDO\UI\Color;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;
use GDO\Session\GDO_Session;

/**
 * CLI utility.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.2
 * @see Method
 */
final class CLI
{
    public static function isCLI() : bool
    {
    	return php_sapi_name() === 'cli';
    }
    
    public static function isInteractive() : bool
    {
    	return stream_isatty(STDIN);
    }
    
    /**
     * Get the CLI username for the current user.
     */
    public static function getUsername() : string
    {
    	return get_current_user();
    }
    
    public static function setupUser() : GDO_User
    {
    	$username = self::getUsername();
    	if (!($user = GDO_User::getByName($username)))
    	{
    		$user = GDO_User::blank([
    			'user_name' => $username,
    			'user_type' => 'member',
    		])->insert();
    	}
    	GDO_User::setCurrent($user, true);
    	return $user;
    }
    
    public static function getSingleCommandLine() : string
    {
    	global $argv;
    	array_shift($argv);
    	return implode(' ', $argv);
    }
    
    ##############
    ### Render ###
    ##############
    public static function flushTopResponse()
    {
    	# Get
    	$response = GDT_Page::instance()->topResponse();
    	# Render
    	echo $response->renderCLI();
    	# Clear
    	self::clearFlash($response);
    }

    public static function getTopResponse() : string
    {
    	$response = GDT_Page::instance()->topResponse();
    	# Render
    	$result = $response->renderCLI();
    	# Clear
    	self::clearFlash($response);
    	return $result;
    }
    
    private static function clearFlash(GDT $response) : void
    {
    	$response->removeFields();
    	if (module_enabled('Session'))
    	{
	    	GDO_Session::remove('redirect_error');
	    	GDO_Session::remove('redirect_message');
    	}
    }
    
    public static function displayCLI(string $html) : string
    {
    	return self::htmlToCLI($html);
    }
    
    /**
     * Turn <br/> into newlines.
     */
    public static function br2nl(string $s, string $nl=PHP_EOL) : string
    {
    	return preg_replace('#< *br */? *>#is', $nl, $s);
    }
    
    /**
     * Turn html into CLI output by stripping tags.
     * @deprecated only needed for bad code.
     */
    public static function htmlToCLI(string $html) : string
    {
    	$html = preg_replace('/<a .*href="([^"]+)".*?>([^<]+)<\\/a>/ius', "$1 ($2)", $html);
    	$html = self::br2nl($html);
    	$html = preg_replace('/<[^>]*>/is', '', $html);
    	$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    	return $html;
    }
    
    #############
    ### Style ###
    #############
    public static function red(string $s) : string { return Color::red($s); }
    public static function green(string $s) : string { return Color::green($s); }
    public static function bold(string $s) : string { return self::typemode($s, '1'); }
    public static function dim(string $s) : string { return self::typemode($s, '2'); }
    public static function italic(string $s) : string { return self::typemode($s, '3'); }
    public static function underlined(string $s) : string { return self::typemode($s, '4'); }
    public static function blinking(string $s) : string { return self::typemode($s, '5'); }
    public static function invisible(string $s) : string { return self::typemode($s, '6'); }
    private static function typemode(string $s, string $mode) : string
    {
    	return sprintf("\033[%sm%s\033[0m", $mode, $s);
    }
    
    ##############
    ### Server ###
    ##############
    /**
     * Simulate PHP $_SERVER vars.
     */
    public static function setServerVars() : void
    {
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['HTTP_HOST'] = GDO_DOMAIN;
        $_SERVER['SERVER_NAME'] = GDO_DOMAIN; # @TODO use machines host name.
        $_SERVER['SERVER_PORT'] = defined('GDO_PORT') ? GDO_PORT : (GDO_PROTOCOL === 'https' ? 443 : 80);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; # @TODO use machines IP
        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
        $_SERVER['REQUEST_URI'] = '/index.php?_mo=' . GDO_MODULE . '&_me=' . GDO_METHOD;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_REFERER'] = 'http://'.GDO_DOMAIN.'/index.php';
        $_SERVER['HTTP_ORIGIN'] = '127.0.0.2';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['SERVER_SOFTWARE']	= 'Apache/2.4.41 (Win64) PHP/7.4.0';
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php';
        $_SERVER['QUERY_STRING'] = 'mo=' . GDO_MODULE . '&me=' . GDO_METHOD;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        # @TODO CLI::setServerVars() use output of locale command?
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
    }
    
    #############
    ### Usage ###
    #############
    private static function showHelp(Method $method)
    {
        return $method->renderCLIHelp();
    }
    
    private static function showMethods(GDO_Module $module)
    {
        $methods = $module->getMethods();
        
        $methods = array_filter($methods, function(Method $method) {
            return (!$method->isAjax()) && $method->isCLI();
        });
        
        $methods = array_map(function(Method $m) {
            return $m->gdoShortName();
        }, $methods);
        
        return GDT_Response::makeWithHTML(t('cli_methods', [
            $module->renderName(), implode(', ', $methods)]));
    }
    
    /**
     * Render help line for gdt parameters.
     * @param GDT[] $fields
     * @return string
     */
    public static function renderCLIHelp(Method $method) : string
    {
        $usage1 = [];
        $usage2 = [];
        $fields = $method->gdoParameterCache();
        foreach ($fields as $gdt)
        {
            if ( (!$gdt->isWriteable()) || ($gdt->isHidden()) )
            {
                continue;
            }
            if ($gdt->isPositional())
            {
            	$label = $gdt->renderLabel();
            	$xmplvars = $gdt->gdoExampleVars();
            	$xmplvars = $xmplvars ? 
            		sprintf('<%s>(%s)', $label, $xmplvars, ) : 
            		sprintf('<%s>', $label);
                $usage1[] = $xmplvars;
            }
            else
            {
            	if ($gdt->getName() !== 'submit')
            	{
	                $usage2[] = sprintf('[--%s=<%s>(%s)]',
	                    $gdt->name, $gdt->gdoExampleVars(), $gdt->getVar());
            	}
            }
        }
        $usage = implode(',', $usage2) . ',' . implode(',', $usage1);
        $usage = trim($usage, ', ');
        $mome = $method->getCLITrigger();
        return ' ' . t('cli_usage', [
            trim(strtolower($mome).' '.$usage), $method->getMethodDescription()]);
    }
    
}

# Required gdo constants
deff('GDO_DOMAIN', 'gdo7.localhost');
deff('GDO_MODULE', 'Core');
deff('GDO_METHOD', 'Welcome');
