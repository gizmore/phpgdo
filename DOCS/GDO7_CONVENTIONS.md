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

# GDOv7 Conventions: Magic Behaviour, Constants and Methods

 - The **foldernames** */inc3p/*, */node_modules/*,
 */bower_components/* and */vendor/*;
 are the default third party library foldernames,
 and ignored at some occassions and processes.

---

# GDOv7 Conventions: DB identifiers

**Table names**, by default, equal their lowercased simple classnames.
E.g. GDO_User has the table name gdo_user.

Column names, should all be a nice prefix of their table's name.

E.g: gdo_user table gets the prefix 'user_'.
Or GDO_UserSetting gets 'uset_' for beeing user settings.

---

# GDOv7 Conventions: CLI Syntax

phpdgo does not use [getopt]().

Instead i wrote an own
[ugly state machine]().


#### examples

---

# GDOv7 Conventions: URLs

SEO urls can be enabled in config.php.
The convention is that variables with a leading dash (_) are not put into url.
Array variables are also not put into the url.
else all variables are appended to the url like /key/value/key/value.
