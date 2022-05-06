# GDOv7 history and changelog

Please refer to the [GDOv6 History](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_HISTORY.md) for earlier versions.


## 7.0.0

A fresh restart of the GDO project.

 - 
 
 - I am now making use of type annotations for scalar- and return values.

 - The core has been rewritten with better CLI and chatbots in mind.
 
 - A slightly bigger bunch of [core modules](../GDO). As their stuff is almost always needed anyway. These do not require an additional module repository.
 
 - [Module_Websocket](../GDO/Websocket/Module Websocket.php) makes now use of the new rendering method ´´´renderBinary´´´ - seems perfect to fuse websocket szenarios with a binary GDT driven protocol.
 
 - There is no more global GDT_Response with hacks and quirks. Methods can return any GDT or a string  now. The response code is stored in Application.
 
 - The [mail module](../GDO/Mail/Module_Mail.php) now takes care of all email setting and configuration stuff. Users can approve emails on their behalf. No more email stuff in Module_Account or elsewhere. Similiar goes for other user settings like gender or geoposition.
 
 - The mail module now needs a MailProvider module. (TODO)

 - [New License](../LICENSE)! GDOv7 is now my exclusive own property. Of course you can still fork, use and enhance [GDOv6](https://github.com/gizmore/gdo6).
 
 - [GDT](../GDO/Core/GDT.php) start completely blank without any attributes now.
 
 - [GDO](../GDO/Core/GDO.php) inherits from [GDT](../GDO/Core/GDT.php) now.
 