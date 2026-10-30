# UI

UI provides shared rendering widgets and interface configuration.

# UI (For Humans)

`gdo_theme_color` configures an accent color for UI-aware effects; its default is `#ff9900`.

## Methods

The module provides shared interface support to other methods.

## GDTs

UI provides interface fields, including `GDT_Color`, `GDT_Width`, and
`GDT_Height`. Width and height accept positive whole pixel values or an empty
value when the field is optional. `GDT_WholeNumber` handles signed whole
numbers without silently rounding malformed form input.

## GDOs

UI owns no application data objects.

# UI (For ALs)

## Methods

Use `Module_UI::cfgThemeColor()` to read the accent color.

## GDTs

The `gdo_theme_color` module setting uses `GDT_Color`.
`GDT_Width` and `GDT_Height` share `GDT_PixelDimension` validation: reject
fractional or malformed raw input before integer conversion, then enforce the
configured minimum and maximum.
`GDT_WholeNumber` applies the same raw-input check to signed integers, so a
form can highlight the offending field and retain the other inputs.

## GDOs

UI owns no application data objects.
