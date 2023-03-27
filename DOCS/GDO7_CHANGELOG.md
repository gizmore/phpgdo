# GDOv7 Changelog (and Roadmap)

###### [INDEX](./__GDO7_INDEX.md)

Welcome to the GDOv7 changelog + combined roadmap.

7.0.2 is almost finished. 7.0.3 is roadmap.

Please read it upside down, from bottom to top and sometimes again.

## 7.0.3 (**planned**)

- Modules marked as 7.0.3 are unit tested in fuzzing mode and all pass phpgdo's StormPHP Code Inspections, well... very rarely with a warning i do not want 
  to disable.
- The source files marked as 7.0.3 are declared as strict.
- The gdo_test.sh fuzzer. now only tries up to a valid value for a parameter. This is faster but misses conjunctured parameters.
- Support for [PostGres DBMS](https:////github.com/gizmore/phpgdo-dbms-mysql)
- The [Module_Docs](https://github.com/gizmore/phpgdo-docs) module is operable and produces usable docs.
  . There are usable docs.phpgdo.com and gdo7.phpgdo.com websites.
- Created a [composer.json](../composer.json) for the GDOv7 core. It can be installed with composer require gizmore/phpgdo.
- New Website Module [Hydra](https://github.com/gizmore/phpgdo-hydra) to monitor services.


## 7.0.2-r2 26.Mar.2023

- [Methods](../GDO/Core/Method.php) can now have a meta image to display in Chat clients.
- The permission level has been removed from permissions.
- StormPHP support added.
- Almost all, and plenty, unit tests are passing. (**in progress**)
- SEO URLs now control their rendering mode via a file suffix like `.txt`, `.html`, `.json` and `.xml`. (thx spaceone)
- A new Core module *DBMS*, which is provided by two packages now; [phpgdo-mysql](https://github.com/gizmore/phpgdo-mysql)
  and [phpgdo-sqlite](https://github.com/gizmore/phpgdo-sqlite).
- A new utility in my automated toolchain for productive environments; [php-preprocessor](https://github.com/gizmore/php-preprocessor). This build
  step eliminates debug and profiler calls enabled via GDO_PREPROCESSOR for zero cost debugging.
- New method Install.Website to generate configurations for various httpd.
- The use of .htaccess is now discouraged for performance reasons. (untested)
- the defaultName() method has been removed. The way for default names is to use ->name() in the constructor. (TODO)
- Files versioned with 7.0.2 are fully type annotated. (TODO)
- ACL settings for user settings are now stored more efficiently beside the settings data.
- GDOs now feature a "softReplace", which is an INSERT ... ON DUPLICATE KEY UPDATE. This is a phpgdo sofware solution, so it should work on sqlite.
- New LGPL [Module_FFMpeg](https://github.com/gizmore/phpgdo-ffmpeg) which offers MP3 encoding and ffmpeg wrappers.
- Refurbished [Module_Boostrap5](https://github.com/gizmore/phpgdo-bootstrap5) and [Module_Boostrap5Theme](https://github.
  com/gizmore/phpgdo-bootstrap5-theme). (**In progress**)
- The automated [todo](../gdo_todo.sh) [generation](GDO7_TODO_AUTO.md) is working again. (TODO)
- The only core dependency was htmlpurifier. This has changed by moving it to [phpgdo-html](https://github.com/gizmore/phpgdo-html), which is a message provider
  using it. The core now simply htmlspecialchars() the input for the output. All more enhanced message providers,
  like [CKEditor](https://github.com/gizmore/phpgdo-ckeditor) or [Markdown](https://github.com/gizmore/phpgdo-markdown), depend on it for security reasons.
- Users may now switch their message editor.
  Implemented [core.health](https://github.com/gizmore/phpgdo/blob/main/GDO/Core/Method/Health.php) for a phpgdo compatible endpoint that renders
  a [health card](https://github.com/gizmore/phpgdo/blob/main/GDO/Core/GDT_HealthCard.php). You can see it in
  action [here](https://kassierercard.org/core/health): [html](https://kassierercard.org/core/health?_fmt=html&_ajax=1) [json](https://kassierercard.org/core/health?_fmt=json) [cli](https://kassierercard.org/core/health?_fmt=cli).
- New Website Module [KassiererCard](https://github.com/gizmore/phpgdo-kassierer-card) - A website about local worker and customer bonus point
  systems. [Demo](https://kassierercard.org)

## 7.0.1 / released 01.Sep.2022

- GDOv7.0.1 will try to convert all worthy gdo6 modules into the GDOv7 API. Quite accomplished now.

- [GDO_Country](https://github.com/gizmore/phpgdo-country/blob/main/GDO_Country.php#L93) now tries to render UTF8 country flags in CLI mode.

- [Module_Perf](../GDO/Perf/Module_Perf.php) now additionally utilizes PHP [getrusage](https://www.php.net/manual/en/function.getrusage.php)

- Memcached now does a fallback to filecache API, if config.php GDO_MEMCACHED ist set to 2. GDO_MEMCACHED is now an INT from 0-2.

- New Account settings with all module settings on one page.

- New [GDT_UserType](../GDO/User/GDT_UserType.php), "link", to link various input sources together. It will be used to link accounts together.

- A working website: HTTP/WWW support beside CLI and JSON. Write methods once and use them everywhere!

- New demo sites, like [Fineprint](https://fineprint.phpgdo.com) which is a web exploit hacking challenge.

- [Module_DOMPDF](https://github.com/gizmore/phpgdo-dompdf) replaces [Module_TCPDPF](https://github.com/gizmore/phpgdo-dompdf) (which never got finished). We
  are now using a HTML to PDF strategy. New GDT render method renderPDF() which defaults renderHTML(). It's the first module to use composer as 3rd party
  library provider. (STALLED)

- [GDT_ACL](../GDO/User/GDT_ACL.php) is now a core GDT and
  has been moved to
  [Module_User](../GDO/User/Module_User.php) ... **finally** :)

- [AutomatedTests](../GDO/Tests/Test)
  now test all GDO to be gdoSaveable() when initial + plugged.

- [AutomatedTests](../GDO/Tests/Test)
  now test all rendering modes on all
  [GDO](./GDO7_GDO.md) + [GDT](./GDO7_GDT.md) automatically.
  Tests are performed With plugged and unplugged initials.

## 7.0.0 / released 22.May.2022 22:22:22, almost :)

A fresh restart of the GDO project.

GDOv7.0.0 comes with *only* the core modules, but all of them cleaned up and *now* unit tested. The goal for this release is a 100% test pass for the new shiny
core.

- New GDT [GDT_Redirect](../GDO/UI/GDT_Redirect.php). This makes [../GDO/Core/Website] only responsible for website page metadata.

- GDO_User->displayNameLabel() and displayName() has both been renamed to renderUserName().

- GDO->displayName() has been renamed to GDO->renderName().

- [Module_TestMethods](https://github.com/gizmore/gdo6-test-methods)
  has been merged into
  [Module_Tests](https://github.com/gizmore/phpgdo/tree/main/GDO/Tests).
  This module features automated test case generation.

- [FileUtil](../GDO/Util/FileUtil.php) is now settled to be in Core, as [Module_File](https://github.com/gizmore/phpgdo-file) is not core anymore. (thx
  flederohr)

- [GDO_Module]s(../GDO/Core/GDO_Module.php) can now have "Friendency" modules. Suggestions that would enhance the features of an installed module.

- These GDTs have been removed: **GDT_IconButton**, **GDT_HTML**, **GDT_DIV**

- Confusion resolved. RENDER_LIST is for <ul> and RENDER_OPTION is for <option>.

- New Domain: phpgdo.com :)

- New protected/config.php variable. GDO_JSON_DEBUG toggles JSON_PRETTY_PRINT globally. Defaults to false.

- [Filewalker](https://github.com/gizmore/php-filewalker) is an own package now, independent from any dependency.

- Support for the bower package manager has been dropped.

- [GDO_User](../GDO/User/GDO_User.php) got these fields moved to separate modules via module setting engine; user_email, user_country, user_credits,
  user_gender, user_real_name, user_password and more...

- [Module_Tests](https://github.com/gizmore/gdo6-tests) *is* now a core module. See [Module_TestMethods](https://github.com/gizmore/phpgdo-test-methods) for
  auto-generated testing. All test cases now pass for the very core and testing modules.

- [Module_Cronjob](https://github.com/gizmore/phpgdo-cronjob) is *not* a core module anymore.

- [Module_File](https://github.com/gizmore/phpgdo-file) is *not* a core module anymore. (thx flederohr)

- [Module_CSS](https://github.com/gizmore/phpgdo-css) is *not* a core module anymore.

- [Module_Javascript](https://github.com/gizmore/phpgdo-javascript) is *not* a core module anymore.

- [Module_Country](https://github.com/gizmore/phpgdo-country) is *not* a core module anymore.

- [Module_Mail](https://github.com/gizmore/phpgdo-mail) is *not* a core module anymore.

- [Module_Admin](https://github.com/gizmore/gdo6-admin) *is* now a core module.

- I am now making use of type annotations for scalar- and return values.

- The core has been rewritten with better CLI and Chatbots in mind.

- A slightly changed bunch of [core modules](../GDO). As their stuff is almost always needed anyway. These do not require an additional module repository.

- [Module_Websocket](https://github.com/gizmore/phpgdo-websocket/Module Websocket.php) makes now use of the new rendering method 
  `renderBinary` - seems 
  perfect to fuse websocket szenarios
  with a binary GDT driven protocol.

- There is no more global GDT_Response with hacks and quirks. Methods can return any GDT now. The response code is stored in Application.

- [Module_Mail](https://github.com/gizmore/phpgdo-mail) now takes care of all email setting and configuration stuff. Users can approve emails on their behalf.
  No more email stuff in [Module_Account](https://github.com/gizmore/phpgdo-account) or elsewhere. Similiar goes for other user settings like password or last
  activity.

- [Module_Mail](https://github.com/gizmore/phpgdo-mail) now needs a [Mailer Provider](https://github.com/gizmore/phpgdo-mailer) module to actually send mails. (
  TODO). Planned is to use own mailer until i find time to write a better 3rd party module.

- [GDT](../GDO/Core/GDT.php) start completely blank without any attributes now. This is important to be able to serve (P)lain(O)ld(O)bjects.

- [Methods](../GDO/Core/Method.php) may now return a GDT, a string or null/none.

- [GDO](../GDO/Core/GDO.php) now inherits from [GDT](../GDO/Core/GDT.php). This means you can return it as a result and call rendering on it.

- [New License](../LICENSE)! GDOv7 is now my exclusive own property. Of course you can still fork, use and enhance [GDOv6](https://github.com/gizmore/gdo6).
