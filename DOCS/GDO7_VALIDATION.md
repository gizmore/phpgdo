# GDOv7 Validation

One of the most frustrating developer tasks is to do the same thing all day over again.
For example write validators in front- and backend.
Here come good news.
With GDOv7 you rarely ever have to write a validator again.
Simply because the inheritance is smart, the validation is flexible and with only a few validators most stuff is covered.
All GDT know how to validate and the validation is automatically consinstent in js, html and php.

*Never* write validators again \o/

## GDOv7 Validation: Not Null

The only default validation is the null check,
implemented in [GDT_Field](../GDO/Core/GDT_Field.php).


## GDOv7 Validation: Strings

[GDT_String](../GDO/Core/GDT_String.php) can validate for lengths, regex patterns and uniqueness.


## GDOv7 Validation: Numbers

[GDT_Int](../GDO/Core/GDT_Int.php) can validate for min/max values and uniqueness.

[GDT_Decimal](../GDO/Core/GDT_Decimal.php) is inheriting from int, using the same validators.


## GDOv7 Validation: Files


## GDOv7 Validation: Unique

The unique validators are implemented in GDT_Int and GDT_String.
As GDT_Object and GDT_Select inherit from these, all unique validations are covered easily.


## GDOv7 Validation: E-Mail

Always funny to hazzle how to validate your email today?
[GDT_Email](../GDO/Mail/GDT_Email.php) is a nice example to show the ease of GDOv7.
Validation is done client and server side with the same regex pattern,
using GDT_String to validate the pattern.

## GDOv7 Validation: Files and URLs

[GDT_Url](../GDO/Net/GDT_Url.php) optionally validates existing URLs,
supporting a few schemes.

[GDT_Path](../GDO/Core/GDT_Path.php) can validate for an existing file (or folder).

GDT_Path also features auto-completion for files on your server.


## GDOv7 Validation: Customized

In some cases you really have to write a validator.
If things get complex, your best friend is GDT_Validator.
It needs a GDT that it shall validate and a validator callback.
Simply call error() on the GDT to check, and you are covered.
