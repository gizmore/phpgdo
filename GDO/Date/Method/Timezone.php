<?php
declare(strict_types=1);
namespace GDO\Date\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Date\GDO_Timezone;
use GDO\Date\GDT_Timezone;
use GDO\Date\Module_Date;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;

/**
 * Change a user's timezone.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class Timezone extends MethodForm
{

	public function isSavingLastUrl(): bool { return false; }

	public function isTransactional(): bool { return false; }

	public function isUserRequired(): bool { return false; }

	public function getFormName(): string { return 'form_tzform'; }

	public function getMethodTitle(): string
	{
		return t('mt_date_timezone');
	}

	public function getMethodDescription(): string
	{
		return t('md_date_timezone', [sitename()]);
	}

	protected function createForm(GDT_Form $form): void
	{
		$tz = GDO_User::current()->getTimezone();
		$form->action(href('Date', 'Timezone'));
		$form->slim()->titleNone();
		$form->addFields(
			GDT_Timezone::make('timezone')->notNull()->initial($tz),
		);
		$form->actions()->addField(
			GDT_Submit::make()->label('btn_set'));
	}

	public function getTimezone(): GDO_Timezone
	{
		return $this->gdoParameterValue('timezone');
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$tz = $this->getTimezone();
		$this->setTimezone($tz, false);
		return parent::formValidated($form);
	}

	public function setTimezone(GDO_Timezone $timezone, $redirect = true): GDT
	{
		$user = GDO_User::current();
		$new = $timezone->getID();
		if ($user->isPersisted())
		{
			Module_Date::instance()->saveUserSetting($user, 'timezone', $new);
		}
		elseif (module_enabled('Session'))
		{
			GDO_Session::set('timezone', $new);
		}
		else
		{
			$user->tempSet('timezone', $new);
		}

		if ($redirect)
		{
			return GDT_Redirect::make()->redirectMessage('msg_timezone_changed', [$new]);
		}

		return GDT_Response::make();
	}

}
