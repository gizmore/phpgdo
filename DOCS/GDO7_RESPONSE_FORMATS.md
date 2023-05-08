# GDOv7 Response Formats

A Method returns a GDT on execution, which is your ultimate result.
A GDT can support the following rendering methods.

- renderHTML() - HTML
- renderForm() - HTML FORM
- renderCell() - HTML table CELL
- renderCard() - HTML cardview
- renderList() - HTML listview
- renderHeader() - HTML table HEADER
- renderFilter() - HTML table FILTER
- renderCLI() - CLI
- renderIRC() - IRC
- renderXML() - @TODO XML
- renderPDF() - @TODO HTML basic pdf cap.
- renderJSON() - JSON
- renderBinary() - GDOv7 Websocket BINARY

PDF is only supported via 3rd party libraries,
and still an early draft / @TODO.


## The GDOv7 BINARY RESPONSE FORMAT

A JSON configuration file is automatically created and requested via AJAX.
All binary transmission relies on that transport contract config.

Roughly it boils down to:

- Ints are binary sized according to GDT_Int::$bytes.
- Strings are 0 terminated urlencoded.
- IEEE Floats are supported.
- Timestamps are 64 bit integers in ms.
- DateTimes are strings.
- Objects are foreign keys according to their primary field(s).
