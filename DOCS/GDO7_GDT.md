# GDOv7 GDT

A GDT is the base class of all almost all classes in the GDOv7 ecosystem. It means "gizmore data type".

Basically it is a collection of types who cover a lot of in and output formats.


# GDOv7 GDT: Output Formats

Currently, GDOv7 supports the following output formats, called rendering methods.

 - CLI
 - HTML (forms, tables, cards, lists, selects, headers, filters, and various UI HTML components)
 - JSON
 - PDF
 - XML
 - GDOWP [GDOWebsocketProtocol](GDO7_GDOWP.md)
 
 
# GDOv7 GDT: Input Formats

Input is accepted from various sources like the [Dog Chatbot](https://github.com/gizmore/phpgdo-dog).
Currently you can control a GDOv7 installation via:

 - CLI ( gdo_adm.sh for the admin and and bin/gdo for normal  users )
 - HTTP ( like a the [GDOv7-Website](https://gdo7.phpgdo.com) )
 - Websocket (todoo)
 - IRC (soon)
 - Telegram (todo)
 - SMS (todo)
 - Email (todo)
 - Twitter (todo)
 

# GDOv7 GDT: Class hierarchy

Here is an overview of the core GDT class hierarchy.
