# GDOv7 I18n

GDOv7 comes with an own i18n impl.

[Trans](../GDO/Language/Trans.php) might not be feature rich,
but should be fast.

English is assumed to be spoken by the user,
as some things are simply not easily or worthly translateable,
like the parameter option key of an input,
like --force. I won't translate option keys
Not gonna do that!
So simple I18n...

    echo t('greet_message');

The lang files are php files that return an array.

the strings are given to printf, so you do "%s" in your lang file.

take a look at the core lang file: [core_en.php](../GDO/Core/lang/core_en.php)

All translations are merged in a big array, so you can, and should, re-use translations.
