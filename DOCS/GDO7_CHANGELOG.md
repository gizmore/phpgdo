# GDOv7 Changelog and Roadmap

Please read from bottom to top :)

Please refer to the gdo6 [GDO_HISTORY.md](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_HISTORY.md) for earlier versions.


## 7.0.4 / scheduled 21.Jul.2022

GDOv7.0.4 will be the first version with SQLite or Postgres support. Else, only bugfixes are applied.

 - New providers for Module_DBMS.
 [MySQL](https://github.com/gizmore/phpgdo-dbms-mysql), [SQLite](https://github.com/gizmore/phpgdo-dbms-sqlite) and [Postgres](https://github.com/gizmore/phpgdo-dbms-postgres).
 

## 7.0.3 / scheduled 26.Jun.2022

GDOv7.0.3 will come with a polished Bootstrap5 theme and feature a new demo site.

 - Created a [composer.json](../composer.json) for the GDOv7 core. It can be installed with composer require gizmore/phpgdo.

 - New demo site [RendlessCode](https://rendless.code.wechall.net) which is a web exploit hacking challenge.

 - Refurbished [Module_Boostrap5](https://github.com/gizmore/phpgdo-bootstrap5) and [Module_Boostrap5Theme](https://github.com/gizmore/phpgdo-bootstrap5-theme).


## 7.0.2 / scheduled 19.Jun.2022

GDOv7.0.2 will have HTTP/WWW support beside CLI and JSON. Write methods once and use them everywhere.


## 7.0.1 / scheduled 05.Jun.2022

GDOv7.0.1 will try to convert all worthy gdo6 modules into the GDOv7 API.

 - [Module_LoginAs](https://github.com/gizmore/gdo6-login-as) has been merged into [Module_Login](https://github.com/gizmore/phpgdo-login).

 - [FileUtil](../GDO/Util/FileUtil.php) is now settled to be in Core, as [Module_File](https://github.com/gizmore/phpgdo-file) is not core anymore. (thx flederohr)

 - New [Module_Gender](../). Nothing fancy like LBQGT, only male and female. User settings, etc.

 - These GDT have been removed: [GDT_IconButton](../)

 - [GDT_ACL]() is now a core GDT.

 - [GDO_Module]s(../GDO/Core/GDO_Module.php) can now have "Friendency" modules. Suggestions that would enhance the features of an installed module.

 - Modules can now come with a [LOGFILE.md](../GDO/Core/LOGFILE.md) which will be the default location for a module's todo and changelog. I chose LOGFILE.md because it fits a nice position in a module directory tree.

 - New / first [GDOv7 Mailer module](https://github.com/gizmore/phpgdo-mailer-gdo). A mailer for symfony or better will follow.
 
 - [Module_DOMPDF](https://github.com/gizmore/phpgdo-dompdf) replaces [Module_TCPDPF](https://github.com/gizmore/phpgdo-dompdf) (which never got finished). We are now using a HTML to PDF strategy. New GDT render method renderPDF() which defaults renderHTML(). It's the first module to use composer as 3rd party library provider.


## 7.0.0 / release 22.May.2022

A fresh restart of the GDO project.

GDOv7.0.0 comes with only *all core* and *some secret/basic* modules, but all of them cleaned up and now unit tested. (on it...)

 - [Filewalker](https://github.com/gizmore/php-filewalker) is an own package now, independent from any dependency.
 
 - Support for the bower package manager has been dropped.

 - [GDO_User](../GDO/User/GDO_User.php) got these fields moved to separate modules via module setting engine; user_email, user_country, user_credits, user_gender, user_real_name and more...

 - [Module_Tests](https://github.com/gizmore/gdo6-tests) *is* now a core module. See [Module_TestMethods](https://github.com/gizmore/phpgdo-test-methods) for auto-generated testing. All test cases now pass for the very core and testing modules.

 - [Module_Cronjob](https://github.com/gizmore/phpgdo-cronjob) is *not* a core module anymore.

 - [Module_File](https://github.com/gizmore/phpgdo-file) is *not* a core module anymore. (thx flederohr)

 - [Module_CSS](https://github.com/gizmore/phpgdo-css) is *not* a core module anymore.

 - [Module_Javascript](https://github.com/gizmore/phpgdo-javascript) is *not* a core module anymore.

 - [Module_Country](https://github.com/gizmore/phpgdo-country) is not a core module anymore.

 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) is not a core module anymore.
 
 - [Module_Admin](https://github.com/gizmore/gdo6-admin) *is* now a core module.
 
 - I am now making use of type annotations for scalar- and return values.

 - The core has been rewritten with better CLI and Chatbots in mind.
 
 - A slightly changed bunch of [core modules](../GDO/). As their stuff is almost always needed anyway. These do not require an additional module repository.
 
 - [Module_Websocket](../GDO/Websocket/Module Websocket.php) makes now use of the new rendering method ´´´renderBinary´´´ - seems perfect to fuse websocket szenarios with a binary GDT driven protocol.
 
 - There is no more global GDT_Response with hacks and quirks. Methods can return any GDT or a string  now. The response code is stored in Application.
 
 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) now takes care of all email setting and configuration stuff. Users can approve emails on their behalf. No more email stuff in [Module_Account](https://github.com/gizmore/phpgdo-account) or elsewhere. Similiar goes for other user settings like gender or geoposition.
 
 - [Module_Mail](https://github.com/gizmore/phpgdo-mail) now needs a [Mailer Provider](https://github.com/gizmore/phpgdo-mailer) module to actually send mails. (TODO). Planned is to use own mailer until i find time to write a better 3rd party module.

 - [GDT](../GDO/Core/GDT.php) start completely blank without any attributes now. This is important to be able to serve (P)lain(O)ld(O)bjects.
 
 - [Methods](../GDO/Core/Method.php) may now return a GDT, a string or null/none.
 
 - [GDO](../GDO/Core/GDO.php) now inherits from [GDT](../GDO/Core/GDT.php). This means you can return it as a result and call rendering on it.

 - [New License](../LICENSE)! GDOv7 is now my exclusive own property. Of course you can still fork, use and enhance [GDOv6](https://github.com/gizmore/gdo6).
