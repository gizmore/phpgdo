<?php
namespace GDO\Date;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;
use GDO\Date\Method\Timezone;

/**
 * Date specific stuff.
 * 
 * - timezone javascript detection. default: on
 * - sidebar timezone select in left panel. default: on
 * - Keeps timezone after user logout.
 * - Time utility helper
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 * @see Time
 */
final class Module_Date extends GDO_Module
{
    public int $priority = 2;
    public function onLoadLanguage() : void
    {
    	$this->loadLanguage('lang/date');
    }
    
    ##############
    ### Config ###
    ##############
    public string $timezone = '1';

    public function getConfig() : array
    {
        return [
            GDT_Checkbox::make('tz_probe_js')->initial('1'),
            GDT_Checkbox::make('tz_sidebar_select')->initial('1'),
        ];
    }
    public function cfgProbeJS() : string { return $this->getConfigVar('tz_probe_js'); }
    public function cfgSidebarSelect() : string { return $this->getConfigVar('tz_sidebar_select'); }
 
    ############
    ### Init ###
    ############
    public function onInstall() : void
    {
    	Install::install($this);
    }
    
    public function onInit() : void
    {
        $user = GDO_User::current();
        $this->timezone = $user->hasTimezone() ?
            $user->getTimezone() : $this->timezone;
        Time::setTimezone($this->timezone);
    }
    
    public function onIncludeScripts() : void
    {
        if ($this->cfgProbeJS())
        {
            if (!GDO_User::current()->hasTimezone())
            {
                $this->addJS('js/gdo_timezone_probe.js');
            }
        }
    }
    
    public function onInitSidebar() : void
    {
        if ($this->cfgSidebarSelect())
        {
            if (!GDO_User::current()->hasTimezone())
            {
                GDT_Page::instance()->leftNav->addField(
                    Timezone::make()->getForm()->slim());
            }
        }
    }
    
    #############
    ### Hooks ###
    #############
    /**
     * Save timezone on authenticated.
     * 
     * @param GDO_User $user
     */
    public function hookUserAuthenticated(GDO_User $user)
    {
    	$user->saveVar('user_timezone', $user->getTimezone());
    }
    
    public function hookUserLoggedOut(GDO_User $user)
    {
    	if ($tz = GDO_Timezone::getById($this->timezone))
    	{
    		Timezone::make()->setTimezone($tz, false);
    	}
    }
    
}
