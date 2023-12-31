<?php
namespace GDO\Language\Method;

use GDO\Core\GDT;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;

final class CheckTranslated extends MethodForm
{

    /**
     * @var string[] $EXCEPTIONS
     */
    private static array $EXCEPTIONS = [
        '%s',
        'sitename',
    ];

    protected function createForm(GDT_Form $form): void
    {
        $form->addFields(
            GDT_Language::make('lang')->notNull(),
        );
        $form->actions()->addFields(
            GDT_Submit::make(),
            GDT_AntiCSRF::make(),
        );
    }

    public function formValidated(GDT_Form $form): GDT
    {
        $lang = $this->gdoParameterVar('lang');

        if ($lang === 'en')
        {
            return $this->message('%s', 'English is the base. All fine!');
        }

        Trans::load('en');
        Trans::load($lang);
        $english = Trans::$CACHE['en'];
        $transed = Trans::$CACHE[$lang];
        foreach ($english as $key => $text)
        {
            if (!Trans::hasKeyIso($lang, $key))
            {
                $this->error('%s', [sprintf('%s is missing translation for %s', $lang, $key)]);
            }
            elseif ($text === $transed[$key])
            {
                if (!in_array($key, self::$EXCEPTIONS, true))
                {
                    $this->error('%s', [sprintf('%s has no real translation for %s', $lang, $key)]);
                }
            }
        }

        foreach (array_keys($transed) as $key)
        {
            if (!isset($english[$key]))
            {
                $this->error('%s', [sprintf('%s has an undefined key: %s', $lang, $key)]);
            }
        }

        return $this->message('%s', ['All done!']);
    }

}
