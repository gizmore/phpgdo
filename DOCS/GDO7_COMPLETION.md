# GDOv7 Completion

## GDOv7 Completion: Method Completion

## GDOv7 Completion: Closest Input

Selects, comboboxes and alike can auto complete input.
If there is only one match starting with a partial input, it is selected.
If there are multiple matches,
a [*GDO*_Error](../GDO/Core/GDO_Error.php)
with suggestions is thrown, or
a [*GDT*_Error](../GDO/UI/GDT_Error.php)
by a [MethodCompletion](../GDO/Core/MethodCompletion.php)

This all works without javascript,
using JS only to enhance the experience. @TODO :D
This is especially important
for [CLI]()
and [Chat applications](https://github.com/gizmore/gdo6-dog).

Module_Javascript
does minify and cache *all*
Javascripts in a single file.

Development machines *always* work on the raw source,
allowing to adapt any 3rd party javascript library effectively.

Module_CSS can do the same to bundle *all* CSS into a single and minified file.


Autocompletion engines can enhance the UX for the web.
