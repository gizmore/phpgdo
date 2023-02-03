# GDOv7 Core: modules and functionality

GDOv7 comes with a slim kernel. Basic things are hopefully done right, like timezones, foreign keys, emoticons, input validation, expression parsing, etc.
GDOv7 is highly modular and quick dirty slim. There is no big asset pipeline, just the F5 toolchain to the max.


## List of GDOv7 core modules

The core (this repository) comes with a few
[modules](../GDO/),
but not all are an required core module and always installed.

[Module_Perf](../GDO/Perf), for examplen,
the performance metric module, is not a core dependency.

Module code can be used without the need of installation,
but of course this is rather pointless.


List of all the modules in this repository,
required Core modules are checked.

 - Crypto
 [x] Date
 [x] DB
 [x] Form
 - Install
 [x] Language
 - Net
 - Perf
 - PHPInfo
 [x] Table
 - Tests
 [x] UI
 [x] User
 

### GDOv7 core modules: Date
