# GDOv7 Response Formats

A GDT can support the following rendering / response formats.

 - HTML
 - HTML AJAX (snippet)
 - HTML FORM
 - HTML CELL
 - HTML HEADER
 - CLI
 - JSON
 - XML
 - GDOv7 BINARY
 

## The GDOv7 BINARY RESPONSE FORMAT

A JSON configuration file is automatically created and requested via AJAX.
All binary transmission relies on that transport contract config.
Strings are 0 terminated.
IEEE Floats are supported.
Timestamps are 64 bit integers.
DateTimes are strings.
Objects are IDs
