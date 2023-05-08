# [GDOv7](../) Conventions

This file describes quirks, conventions and some implementation details.

Some conventions exist for performance reasons,
others are maybe drafts or disabled.

---

# GDOv7 Conventions: Wording and Definition of Terms.

With *Wording* i mostly mean naming conventions.

- Output functions: If you write GDO helper functions to render a GDT named XXXXX,
`renderXXXXXX()` shall return the rendering of the current `GDT->getVar()` state.
You may also craete a stateless version. Please name them like `displayXXXXXX(string $var)`. 

- GDT's shall make use of my accessor pattern for public accessible attributes. (@TODO: elaborate)

- functions shall start with special scatterered
  first letters, like lang($lang):self - a setter -
  or timezone($tz):self - anotherSetter.
  (**@TODO:**) protected overrides shall end with the prefix of it's return type, like function versionMOD, varGDO, varGDT, etc.

---

## GDOv7 Conventions: Magic behaviour, constants and methods

- The **foldernames** "*/inc3p/*", "*/node_modules/*",
  "*/bower_components/*" and "*/vendor/*";
  are the default third party library foldernames,
  and ignored at some occassions and processes.

- **Filepathes** distingush between file and folder by their last character.
Directories simply shall end with a "`/`".

---

## GDOv7 Conventions: GDT Data Conversion

A GDT needs to implement `toVar($value):string` and `toValue(string $var)`,
to be able to convert between `$var`(string) and `$value`(any).

The base class [GDT](../GDO/Core/GDT.php) simply returns a string all times.

The name `$var` always means the *DB* representation,
and is stored by a GDT always as `string|null`.

The name `$value` implies it could be anything,
and depends on the GDT to convert hence and forth.

As an example,
[GDT_Int](../GDO/Core/GDT_Int.php)
converts between `string|null` and `int|null`,
and [GDT_String](../GDO/Core/GDT_String.php),
simply between `string|null` and `string|null`.

The names `$var` and `$value` are used within the hole project.


## GDOv7 Conventions: Time Conversion

I wrote my own implementation of a time utility.

The core module [Date](../GDO/Date/) ships with
GDT for duration, year, month, week, date, time, datetime, timezone and a few more.

The [Time utility](../GDO/Date/Time.php) class
is used, following these naming and behaviour conventions
for the required [primitive datatypes](https://en.wikipedia.org/wiki/Primitive_data_type)
and method names in the time core:

- `time` refers to a unix timestamp `float|int|null`,
with default 3 digits precision when converted to a `date`.
- `date` refers to db representation, similar to
[RFC3339](https://datatracker.ietf.org/doc/html/rfc3339). 
String lengts of `10, 16, 19 and 23` are supported and valid.
23 byte dates also have a precision in milliseconds.
- `tz|timezone` refers to a [GDO_Timezone](../GDO/Date/GDO_Timezone.php) database ID.
IDs are not converted to `int` in GDO.
- `datetime` refers to a PHP `\Datetime` object.
- `format` refers to `PHP date() format`.
- 
---

## GDOv7 Conventions: DB Keys

GDO does not make any conventions of rowids or auto increments.
Composite keys can be used nicely.
However, the primary keys have to be the first field(s) in your `gdoColumns()`,
for a little performance gain.

---

## GDOv7 Conventions: DB identifiers

**Table names**, by default, equal their lowercased simple classnames.
E.g. GDO_User has the table name gdo_user.

Column names should all have a nice prefix like their table's name.

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

After *optionally* putting `phpgdo/bin` into your system environment `PATH` variable,

You can issue commands from the commandline by invoking the `gdo` command.

This should work on windows and linux alike, as long as you got git*4windows* installed.

General:

- Positional parameters are required in order.
- Optional parameters start with a double dash, e.g: --page=2
- Separate parameters by comma.
- Escape commas with double commas.

## GDOv7 Conventions: CLI Examples

Let's learn by a few examples.

```
gdo core.version # specify method with `module.method` as first parameter. For MethodForm, The default action is pressing the first submit button.
gdo cli.ekko hello world, gizzy! # Two positional parameters.
gdo echo hello world,, gizzy! # Single parameter with escaped comma.
gdo cli.collect . # invoke with 1 positional parameter (current directory)
gdo mail.send giz,Hello,, my friend,This is the mail body # will send an email to me 
```

---

## GDOv7 Conventions: SEO URLs

SEO urls can be enabled in config.php by setting `GDO_SEO_URLS` to `true`.

SEthereisonly

The convention is that variables with a leading dash `_`, and arrays, are not put into the path.
All other variables are appended to the url,
like `/key/var/key/var`.

@TODO IDEA: SEO_URLs do not allow custom `&parameters`, only one single ?p=0xdata.
This way, a user cannot introduce custom get paramters.

---

## GDOv7 Conventions: HTTP @DRAFT v7.1.0

The following HTTP status codes shall be used.

Yes, i know this feels wrong at so many points.

- `200` GET OK - `GET` request successful.
- `203` SAVED - `POST` request successful.
- `403` GDT ERROR - The Method `gdoParamter()` is errorneous.
- `409` GDT ERROR - The Method `gdoParamter()` is errorneous.
- `500` GDO EXCEPTION - An exception was thrown.

---

## GDOv7 Conventions: HTML escaping @DRAFT

There is a global helper function named `html($s)`.
It escapes a string according to the current rendering mode.

By default, traits like
[WithText](../GDO/UI/WithText.php),
[WithTitle](../GDO/UI/WithTitle.php),
[WithSubtitle](../GDO/UI/WithSubTitle.php),
do **not** escape their attributes upon printing.

You have to *opt-in* for those clock cycles.
Simply use `$gdt->escaped()` on your GDT and only escape real user input.


- CLI escapes for use in shell.
- IRC removes colors and text style.
- HTML escapes htmlspecialchars.
- WS is a tramsparent transport layer; No escape needed? @TODO
- XML escapes like HTML? @TODO

OLD @DRAFT / NOT TRUE ANYMORE, but consideradble, yeah.

These conventions exist for performance reasons,
and *can* lead to *insecure* and **wrong** html code.

- HTML attributes **must** be using double quotes `"`, to safe the replacement of `'`.
- Double quotes are replaced by a single quote. 
This way, we replace always the same character count.
- The ampersand `&` is replaced by a plus `+`. (slow version only)

This **could** *theoretically* safe lots of clock cycles in a hot-spot... But we need to replace more.

The brackets `<>` are not replaced by default, but only in a slower version of `html()`.


That's it, for a nice performance stunt. :)


(c)2023 by gizmore@wechall.net
