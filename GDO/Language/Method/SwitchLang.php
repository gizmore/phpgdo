<?php
namespace GDO\Language\Method;

use GDO\Core\GDO_ArgError;
use GDO\Core\GDT;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;

/**
 * A form to switch language.
 */
final class SwitchLang extends MethodForm
{

    public function isUserRequired(): bool
    {
        return false;
    }

    public function getMethodTitle(): string
    {
        return t('language');
    }

    /**
     * @throws GDO_ArgError
     */
    public function getMethodDescription(): string
    {
        if ($this->getLanguage(false))
        {
            return t('md_switch_language', [$this->getLanguage()->renderName()]);
        }
        else
        {
            return t('md_switch_language2');
        }
    }

    protected function createForm(GDT_Form $form): void
    {
        $form->titleNone();
        $form->addFields(
            GDT_Url::make('_ref')->allowInternal()->writeable(false)->hidden()->initial(urldecode($_SERVER['REQUEST_URI'])),
            GDT_Language::make('lang')->initial(Trans::$ISO),
        );
        $form->actions()->addField(GDT_Submit::make());
        $form->slim();
        $form->action($this->href());
    }

    /**
     * @throws GDO_ArgError
     */
    protected function getLanguage(bool $throw = true): ?GDO_Language
    {
        return $this->gdoParameterValue('lang', $throw, $throw);
    }

    /**
     * @throws GDO_ArgError
     */
    public function formValidated(GDT_Form $form): GDT
    {
        # Set new ISO language
        $lang = $this->getLanguage();
        $iso = $lang->getISO();
        Trans::setISO($iso);

        # Redirect back to new language
        if (!($ref = $this->gdoParameterVar('_ref')))
        {
            $ref = GDT_Redirect::hrefBack();
        }
        $ref = GDT_Link::replacedHREFS($ref, '_lang', $iso);

        # Save new iso
        $user = GDO_User::current()->persistent();
        $user->saveSettingVar('Language', 'language', $iso);

        # Do it
        return GDT_Redirect::make()->
        href($ref)->
        redirectMessage('msg_language_set', [$lang->renderName()]);
    }

}

