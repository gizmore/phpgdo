# GDOv7 I18n

GDOv7 comes with an own i18n impl.

[Trans](../GDO/Language/Trans.php) might not be feature rich,
but should be fast.

English is assumed to be spoken by the users,
as some things are simply not easy or worthy to translat,
like the parameter option key of an input,
like --force. I won't translate option keys
Not gonna do that!


## GDOv7 I18n: Implementation

The I18n API is implemented in a single file, [Trans](../GDO/Language/Trans.php).
Only modules can have a lang file.
The language files do just return a PHP array.
These files get merged and stored in a cache.

There is no support for foreign numerals, the variable replacements are simple format strings.

There is no support for dialects, like ISO is just 2 letter code, not en_UK.



## GDOv7 I18n: API

There is just a very functions.

Global wrappers exist, to ease the using of the API:

- `t(string $key, array $args = null)` - for translating into the current user's language.
- `ten(string $key, array $args = null)` - for translating into the main language, ( `GDO_LANGUAGE` in config.php )
- `tiso(string $iso, string $key, array $args = null)` - for translating into the specified `$iso` language the current language.
- `tusr(GDO_User $user, string $key, array $args = null)` - for translating into the specified user's language.


The [Trans](../GDO/Language/Trans.php) class is completely static, and has the following API:

- `setISO(string $iso)` - To switch the current language.
- ``
-
-



So simple I18n...

    echo t('greet_message');

The lang files are php files that return an array.

the strings are given to printf, so you do "%s" in your lang file.

take a look at the core lang file: [core_en.php](../GDO/Core/lang/core_en.php)

All translations are merged in a big array, so you can, and should, re-use translations.
