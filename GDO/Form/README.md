# Form

Form provides input forms and validation for PHPGDO methods.

# Form (For Humans)

Forms normally use standard URL-encoded POST data. Methods accepting native
file uploads can opt in to multipart encoding with `GDT_Form::multipart()`.

## Methods

The module provides the form framework used by form-based methods.

## GDTs

`GDT_Form` renders and validates a form and its fields.

## GDOs

This module has no module-owned data tables.

# Form (For ALs)

## Methods

Form-based methods create and validate their inputs with `MethodForm`.

## GDTs

`GDT_Form::multipart(true)` renders `enctype="multipart/form-data"` on the
HTML `<form>` element. It defaults to false; `multipart(false)` restores the
ordinary browser default. Both the default and Bootstrap5 templates must
honour the setting.

## GDOs

This module has no module-owned data tables.
