<?php
namespace GDO\Date\Method;

use GDO\Core\GDO_ArgError;
use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Date\GDO_Timezone;
use GDO\Date\GDT_Timezone;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Detect timezone by name.
 * Call Timezone method with resolved id.
 *
 * @author gizmore
 */
final class TimezoneDetect extends MethodForm
{

	public function getFormName(): string { return 'tzform'; }

	public function isUserRequired(): bool { return false; }

	public function isTransactional(): bool { return false; }

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
		$tz = GDT_Timezone::make('timezone')->notNull();
		$form->addFields(
			$tz,
// 			GDT_Validator::make('validTimezone')->validator($form, $tz, [$this, 'validateTimezoneName']),
		);
		$form->actions()->addField(GDT_Submit::make()->label('btn_set'));
	}

    /**
     * @throws GDO_ArgError
     */
    public function formValidated(GDT_Form $form): GDT
	{
        $timezone = $this->gdoParameterValue('timezone');
		$inputs = [
			'timezone' => $timezone->getID(),
			'submit' => '1',
		];
		return Timezone::make()->executeWithInputs($inputs);
	}

//	public function validateTimezoneName(GDT_Form $form, GDT_String $string, $value)
//	{
//		if (!($this->tz = GDO_Timezone::getBy('tz_name', $value->getName())))
//		{
//			return $string->error('err_unknown_timezone');
//		}
//		return true;
//	}

}
