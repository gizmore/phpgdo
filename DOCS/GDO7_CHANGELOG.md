# GDOv7 Changelog (and Roadmap)
###### [INDEX](./_GDO7_INDEX.md)


Welcome to the GDOv7 changelog + combined roadmap + documentation.


Please read it upside down, from bottom to top and sometimes again.

Please **do not** refer to the old
gdo6 (https://github.com/gizmore/gdo6)
GDO_HISTORY.md
](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_HISTORY.md)
 for earlier versions *anymore*. Delinked sic.


## GDOv7 Versioning

A @version in an annotation of a GDOv7 php file means the featureset the code already will be compatible with, not it's module version installed.

A version in this file denotes the version a feature will officially be available. 
This might be wrong.
Some fine future features from the function factory are already available in earlier versions, maybe even backported to their branches.
Those are marked with a "**?!**".

A version in the module database denotes the installed GDO_Module->$version. This is *7.0.0* by default.
If we now increase to 7.0.1, hooks will be triggered,
including the event of a full table rebuild on a schema change, etc... i want to keep version bumps rare.


## 7.0.4 / scheduled 9.Aug.2022

 GDOv7.0.5 will introduce full support for french and latvian.
 
Just kidding *:)*

- [AutomatedTests](../GDO/Tests/Test/AutomatedTests)
now test all rendering modes on **all themes** and all
[GDO](GDO7_GDO.md) + [GDT](GDO7_GDT.md) automatically.
Tests are performed With plugged and unplugged initials.



## 7.0.3 / scheduled 21.Jul.2022

 - Support for SQLite **or** Postgres support. What shall i try first?

 - The [Module_Docs](https://github.com/gizmore/phpgdo-docs) module is operable and produces usable docs.

 . There are usable docs.phpgdo.com and gdo7.phpgdo.com websites.

 - New providers for Module_DBMS.
 
 [MySQL](https://github.com/gizmore/phpgdo-dbms-mysql), [SQLite](https://github.com/gizmore/phpgdo-dbms-sqlite) and [Postgres](https://github.com/gizmore/phpgdo-dbms-postgres).
 

## 7.0.2 / scheduled 26.Jun.2022

 - New [GDT_UserType](../GDO/User/GDT_UserType.php), "link", to link various input sources together.

 - Created a [composer.json](../composer.json) for the GDOv7 core. It can be installed with composer require gizmore/phpgdo.

 - New demo sites, like [RendlessCode](https://rendless.code.wechall.net) which is a web exploit hacking challenge.

 - Refurbished [Module_Boostrap5](https://github.com/gizmore/phpgdo-bootstrap5) and [Module_Boostrap5Theme](https://github.com/gizmore/phpgdo-bootstrap5-theme).


## 7.0.1 / scheduled 05.Jun.2022

GDOv7.0.1 will try to convert all worthy gdo6 modules into the GDOv7 API.

 - A working website: HTTP/WWW support beside CLI and JSON. Write methods once and use them everywhere!

 - The automated [todo](../gdo_todo.sh) [generation](GDO7_TODO_AUTO.md) is working again.

 - New [Module_Gender](../). Nothing fancy like LBQGT, only male and female. User settings, etc.

 - New / first [GDOv7 Mailer module](https://github.com/gizmore/phpgdo-mailer-gdo). A mailer for symfony or better will follow.
 
 - [Module_DOMPDF](https://github.com/gizmore/phpgdo-dompdf) replaces [Module_TCPDPF](https://github.com/gizmore/phpgdo-dompdf) (which never got finished). We are now using a HTML to PDF strategy. New GDT render method renderPDF() which defaults renderHTML(). It's the first module to use composer as 3rd party library provider.
 
 - [GDT_ACL](../GDO/User/GDT_ACL.php) is now a core GDT and 
has been moved to
[Module_User](../GDO/User/Module_User.php) ... **finally** :)

 - [AutomatedTests](../GDO/Tests/Test/AutomatedTests)
now test all GDO to be gdoSaveable() when initial + plugged.

 - [AutomatedTests](../GDO/Tests/Test/AutomatedTests)
now test all rendering modes on all
[GDO](GDO7_GDO.md) + [GDT](GDO7_GDT.md) automatically.
Tests are performed With plugged and unplugged initials.


## 7.0.0 / released 22.May.2022 22:22:22, almost :)

A fresh restart of the GDO project.

GDOv7.0.0 comes with *only* the core modules, but all of them cleaned up and *now* unit tested. The goal for this release is a 100% test pass for the new shiny core.

 - GDO_User->displayNameLabel() and displayName() has both been renamed to renderUserName().
 
 - GDO->displayName() has been renamed to GDO->renderName().

 - [Module_TestMethods](https://github.com/gizmore/gdo6-test-methods) 
 has been merged into
 [Module_Tests](https://github.com/gizmore/phpgdo/tree/main/GDO/Tests).
 This module features automated test case generation.

 - [FileUtil](../GDO/Util/FileUtil.php) is now settled to be in Core, as [Module_File](https://github.com/gizmore/phpgdo-file) is not core anymore. (thx flederohr)

 - [GDO_Module]s(../GDO/Core/GDO_Module.php) can now have "Friendency" modules. Suggestions that would enhance the features of an installed module.

 - Modules can now come with a [LOGFILE.md](../GDO/Core/LOGFILE.md) which will be the default location for a module's changelog. I chose LOGFILE.md because it fits a nice position in a module directory tree.

 - These GDT have been removed: [GDT_IconButton](../)

 - Confusion resolved. RENDER_LIST is for <ul> and RENDER_CHOICE is for <option>.

 - New Domain: phpgdo.com :)

 - New protected/config.php variable. GDO_JSON_DEBUG toggles JSON_PRETTY_PRINT globally. Defaults to false.

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
