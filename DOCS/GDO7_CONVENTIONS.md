# GDOv7 Conventions

This file describes quirks, conventions and some implementation details.

---

# GDOv7 Conventions: Wording and Definition of Terms.

With *Wording* i mostly mean function name conventions.

 - var means db serialized string; value means deserialized object.
[GDT_String](../GDO/Core/GDT_String.php)
simply does nothing for var/value conversion.
[GDT_Int](../GDO/Core/GDT_Int.php)'s
value type is `int`.

 - Output functions: renderXXX() shall return a the GDT's state. displayXXX($var) shall return a parmeterized $var state.

 - GDT's shall make use of my accessor pattern for public accessible member variables. (@TODO: elaborate)

---

## GDOv7 Conventions, Magic behaviour, constants and methods

 - The **foldernames** */inc3p/*, */node_modules/*,
 */bower_components/* and */vendor/*;
 are the default third party library foldernames,
 and ignored at some occassions and processes.
 
 - **Filepathes** distingush their type between file and folder by the last character. Directories end with a `/`.

---

## GDOv7 Conventions: DB Keys

GDO does not make any conventions of rowids or auto increments.
Composite keys can be used nicely.
However, your primary keys have to be the first field(s) in your GDO columns,
for a little performance gain.

---

## GDOv7 Conventions: DB identifiers

**Table names**, by default, equal their lowercased simple classnames.
E.g. GDO_User has the table name gdo_user.

Column names, should all be a nice prefix of their table's name.

E.g: gdo_user table gets the prefix 'user_'.
Or GDO_UserSetting gets 'uset_' for beeing user settings.

---

## GDOv7 Conventions: CLI Syntax

phpdgo does not use [getopt]().

Instead i wrote an own
[ugly state machine]()
.

Let's learn by a few examples...

```
gdo core.version # specify method with `module.method` as first parameter.

gdo cli.echo "hello world" # The first and second parameter are separated by a space.

gdo cli.echo hello,world # But from the second on, they are seperated by comma `,`.

# To escape them, try this.

gdo mail.send giz,hi there,this is  the third paramter,, the mail body. \
You escape a comma by doubling it,, you know?
```

---

## GDOv7 Conventions: SEO URLs

SEO urls can be enabled in config.php by setting `GDO_SEO_URLS` to true.

The convention is that variables with a leading dash `_` and arrays are not put into the path.
All other variables are appended to the url,
like `/key/value/key/value`.

---

## GDOv7 Conventions: HTML escaping

These conventions exist for performance reasons,
and *can* lead to **insecure code**!

HTML attributes **must** be using double quotes `"`, to safe the replacement of `'`.
Double quotes are replaced by a single quote.
This way, we replace always the same character count,
and can *theoretically* safe lots of clock cycles in a hot-spot... But we need to replace more.

The brackets `<>` are replaced by `{}`.

The ampersand `&` is replaced by a plus `+`.

That's it, for a nice performance stunt. :)

Sample Code in [GDOv7.php](../GDO7.php)

````
return Application::$INSTANCE->isHTML() ?
str_replace(['&','"','<','>'],
            ['+','\'','{','}'], $html) : $html;
````

(c)2023 by wechall.net
