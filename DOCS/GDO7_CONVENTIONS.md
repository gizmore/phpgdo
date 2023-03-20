# [GDOv7](./_GDO7) Conventions

This file describes quirks, conventions and some implementation details.

Some conventions exist for performance reasons,
and are still drafts or disabled.

---

# GDOv7 Conventions: Wording and Definition of Terms.

With *Wording* i mostly mean naming conventions.

- `$var` means db serialized string; `$value` means deserialized.
  [GDT_String](../GDO/Core/GDT_String.php)
  simply does nothing for var/value conversion.
  [GDT_Int](../GDO/Core/GDT_Int.php)'s
  value type is `int`. (**@TODO:**) Nope make it string.

- Output functions: renderXXX() shall return a the GDT's state. displayXXX($var) shall return a parmeterized $var state.

- GDT's shall make use of my accessor pattern for public accessible member variables. (@TODO: elaborate)

- functions shall start with special scatterered
  first letters, like lang($lang):self - a setter -
  or timezone($tz):self - anotherSetter.
  (**@TODO:**) protected overrides shall end with the prefix of it's return type, like function versionMOD, varGDO, varGDT, etc.

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

## GDOv7 Conventions: Boolean Parameters.

Booleans
([GDT_Checkbox](../GDO/Core/GDT_Checkbox.php))
optionally can be three-state,
by using their `GDT_Checkbox::undertemined($bool=true)` setter.
and are implemented as
[GDT_Select](../GDO/Core/GDT_Select.php).

- Optional GDT_Checkbox parameters shall **always** be opt-in.
- The serialized `toVar()` string for the undetermined state is `'2'`.
- The `toValue()` return type is `bool|null`.

---

## GDOv7 Conventions: CLI Syntax

phpdgo does **not** use
[getopt](https://www.php.net/manual/en/function.getopt.php).

Instead i wrote an own
[ugly state machine](../GDO/Core/Expression/Parser.php).

There is room for improvement.

Let's learn by a few examples...

## GDOv7 Conventions: CLI Syntax

After putting `phpgdo/bin` into your system environment `PATH` variable,

You can issue commands from the commandline by invoking the `gdo` command.

This should work on windows and linux, as long as you got git*4windows* installed.

General:

- Positional parameters are required in order.
- Optional parameters start with a double dash, e.g: --page=2
- Separate parameters by comma.
- Escape commas with double commas.

Let's learn by example.

```
gdo core.version # specify method with `module.method` as first parameter. For MethodForm, The default action is pressing the first submit button.
gdo cli.ekko hello world, gizzy! # Two positional parameters.
gdo echo hello world,, gizzy! # Single parameter with escaped comma.
```

---

## GDOv7 Conventions: SEO URLs

SEO urls can be enabled in config.php by setting `GDO_SEO_URLS` to true.
thereisonly

The convention is that variables with a leading dash `_` and arrays are not put into the path.
All other variables are appended to the url,
like `/key/value/key/value`.

@TODO IDEA: SEO_URLs do not allow custom `&parameters`, only one single ?p=0xdata.
This way, a user cannot introduce custom get paramters.

---

## GDOv7 Conventions: HTML escaping

OLD/NOT TRUE ANYMORE, but consideradble, yeah.

These conventions exist for performance reasons,
and *can* lead to *insecure* and **wrong** html code.

- HTML attributes **must** be using double quotes `"`, to safe the replacement of `'`.
- Double quotes are replaced by a single quote.
- This way, we replace always the same character count.

This **could** *theoretically* safe lots of clock cycles in a hot-spot... But we need to replace more.

The brackets `<>` are replaced by `{}`.

The ampersand `&` is replaced by a plus `+`.

That's it, for a nice performance stunt. :)

Sample Code in [GDO7.php](../GDO7.php)

````
return Application::$INSTANCE->isHTML() ?
# @TODO Lesson: Implement fasthtml/ht(string $toHTMLspecialchar): loop and count occurances of each replacement and map accordingly?
if (preg_match('/[&"]/iD')) # speedup?
trick? str_replace(['&','"','<','>'],
            ['+','\'','d','b'], $html) : $html;
````

(c)2023 by gizmore@wechall.net
