# GDOv7 Response Formats

A GDT can support the following rendering methods.
PDF is supported via html2pdf libraries.

 - renderHTML() - HTML
 - renderForm() - HTML FORM
 - renderCell() - HTML table CELL
 - renderCard() - HTML cardview
 - renderHeader() - HTML table HEADER
 - renderFilter() - HTML table FILTER
 - renderCLI() - CLI
 - renderXML() - XML
 - renderPDF() - HTML basic pdf cap.
 - renderJSON() - JSON
 - renderBinary() - GDOv7 BINARY
 

## The GDOv7 BINARY RESPONSE FORMAT

A JSON configuration file is automatically created and requested via AJAX.
All binary transmission relies on that transport contract config.

Roughly it boils down to:

 - Ints are binary sized according to GDT_Int::$bytes.
 - Strings are 0 terminated urlencoded.
 - IEEE Floats are supported.
 - Timestamps are 64 bit integers in ms.
 - DateTimes are strings.
 - Objects are IDs.
