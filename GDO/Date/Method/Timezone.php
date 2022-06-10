<?php
namespace GDO\Date\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Date\GDT_Timezone;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_User;
use GDO\Core\GDT_Response;
use GDO\Session\GDO_Session;
use GDO\Date\GDO_Timezone;
use GDO\UI\GDT_Redirect;
use GDO\Date\Module_Date;

/**
 * Change a user's timezone.
 * 
 * @author gizmore
 */
final class Timezone extends MethodForm
{
	public function saveLastUrl() : bool { return false; }
    public function isTransactional() : bool { return false; }
    public function isUserRequired() : bool { return false; }
    
    public function getFormName() : string { return 'tzform'; }
    
    public function getMethodTitle() : string
    {
    	return t('ft_date_timezone');
    }

    public function getMethodDescription() : string
    {
    	return t('md_date_timezone', [sitename()]);
    }
    
    public function createForm(GDT_Form $form) : void
    {
        $tz = GDO_User::current()->getTimezone();
        $form->action(href('Date', 'Timezone'));
        $form->slim()->noTitle();
        $form->addFields(
            GDT_Timezone::make('timezone')->notNull()->initial($tz),
        );
        $form->actions()->addField(
            GDT_Submit::make()->label('btn_set'));
    }
    
    public function getTimezone() : GDO_Timezone
    {
    	return $this->gdoParameterValue('timezone');
    }

    public function formValidated(GDT_Form $form)
    {
    	$tz = $this->getTimezone();
        $this->setTimezone($tz, false);
        return parent::formValidated($form);
    }
    
    public function setTimezone(GDO_Timezone $timezone, $redirect=true)
    {
        $user = GDO_User::current();
        $old = $user->getTimezone();
        $new = $timezone->getID();
        if ($old != $new)
        {
            if ($user->isPersisted())
            {
	        	Module_Date::instance()->saveUserSetting($user, 'timezone', $new);
            }
            else
            {
                if (class_exists('\\GDO\\Session\\GDO_Session', false))
                {
                    GDO_Session::set('timezone', $new);
                }
                else
                {
                    $user->tempSet('timezone', $new);
                }
            }
            if ($redirect)
            {
            	return GDT_Redirect::make()->redirectMessage('msg_timezone_changed', [$new]);
            }
        }
        else
        {
            if ($redirect)
            {
            	return GDT_Redirect::make()->redirectMessage('err_nothing_happened');
            }
        }
        
        return GDT_Response::make();
    }
    
}
