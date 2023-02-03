# GDOv7 Conventions

This document describes quirks and conventions for implementation details.

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

