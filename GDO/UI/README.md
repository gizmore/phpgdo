# UI

UI provides shared rendering widgets and interface configuration.

# UI (For Humans)

`gdo_theme_color` configures an accent color for UI-aware effects; its default is `#ff9900`.

## Methods

The module provides shared interface support to other methods.

## GDTs

UI provides interface fields, including `GDT_Color`.

## GDOs

UI owns no application data objects.

# UI (For ALs)

## Methods

Use `Module_UI::cfgThemeColor()` to read the accent color.

## GDTs

The `gdo_theme_color` module setting uses `GDT_Color`.

## GDOs

UI owns no application data objects.
