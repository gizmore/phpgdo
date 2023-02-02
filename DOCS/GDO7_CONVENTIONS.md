# GDOv7 Conventions

This document describes quirks and conventions for implementation details.


# GDOv7 Conventions: DB identifiers

Table names, by default, equal lowercased simple classnames.

Column names, should all be a nice prefix of the table name.
E.g: 'user_' or 'uset_' for user settings.


# GDOv7 Conventions: URLs

SEO urls can be enabled in config.php.
The convention is that variables with a leading dash (_) are not put into url.
Array variables are also not put into the url.
else all variables are appended to the url like /key/value/key/value.
