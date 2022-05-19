# GDOv7 History and Changelog

Please refer to the [GDOv6 History](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_HISTORY.md) for earlier versions.


## 7.0.0 / released 06.May.2022

A fresh restart of the GDO project. :)

 - 

 - [Module_Tests](https://github.com/gizmore/gdo6-tests) *is* now a core module. See [Module_TestMethods](https://github.com/gizmore/phpgdo-test-methods)

 - [Module_CSS](https://github.com/gizmore/phpgdo-css) is not a core module anymore.

 - [Module_Country](https://github.com/gizmore/phpgdo-country) is not a core module anymore.

 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) is not a core module anymore.
 
 - [Module_Admin](https://github.com/gizmore/gdo6-admin) *is* now a core module.
 
 - I am now making use of type annotations for scalar- and return values.

 - The core has been rewritten with better CLI and Chatbots in mind.
 
 - A slightly bigger bunch of [core modules](../GDO). As their stuff is almost always needed anyway. These do not require an additional module repository.
 
 - [Module_Websocket](../GDO/Websocket/Module Websocket.php) makes now use of the new rendering method ´´´renderBinary´´´ - seems perfect to fuse websocket szenarios with a binary GDT driven protocol.
 
 - There is no more global GDT_Response with hacks and quirks. Methods can return any GDT or a string  now. The response code is stored in Application.
 
 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) now takes care of all email setting and configuration stuff. Users can approve emails on their behalf. No more email stuff in [Module_Account](https://github.com/gizmore/phpgdo-account) or elsewhere. Similiar goes for other user settings like gender or geoposition.
 
 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) now needs a MailProvider module. (TODO). Planned is to use own mailer until i find time to write a better 3rd party module.

 - [New License](../LICENSE)! GDOv7 is now my exclusive own property. Of course you can still fork, use and enhance [GDOv6](https://github.com/gizmore/gdo6).
 
 - [GDT](../GDO/Core/GDT.php) start completely blank without any attributes now.
 
 - [GDO](../GDO/Core/GDO.php) now inherits from [GDT](../GDO/Core/GDT.php). This means you can return it as a result and call rendering on it.
