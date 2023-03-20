# GDOv7 Validation

One of the most frustrating developer tasks is to do the same thing all day over again.
For example write validators.
Here come good news.
With GDOv7 you rarely ever have to write a validator again.
Simply because the inheritance is smart, the validation is flexible and with only a few validators most stuff is covered.

## GDOv7 Validation: Not Null

## GDOv7 Validation: Strings

## GDOv7 Validation: Numbers

## GDOv7 Validation: Files

## GDOv7 Validation: Unique

## GDOv7 Validation: E-Mail

Always funny to hazzle how to validate your email today?
[GDT_Email](../) is a nice example to show the ease of GDOv7.

## GDOv7 Validation: Customized

In some cases you really have to write a validator.
If things get complex, your best friend is GDT_Validator.
It needs a GDT that it shall validate and a validator callback.
Simply call error() on the GDT to check, and you are covered.
