# GDOv7 Core: modules and functionality

GDOv7 comes with a profound kernel.
Basic things are hopefully done right,
like timezones, foreign keys, emoticons,
input validation, expression parsing, etc.
GDOv7 is highly modular and quick and dirty.
There is no big asset pipeline,
just an **F5 toolchain**, optimized for maximum performance and productivity.


## List of GDOv7 core modules

The core (this repository) comes with a few
[modules](../GDO/),
but not all are an required core module and always installed.

[Module_Perf](../GDO/Perf), for examplen,
the performance metric module, is not a core dependency.

Module code can be used without the need of installation,
but of course this is rather pointless.

List of all shipped modules in the core repository.
**Required** and always installed core modules are bolded.

- [Admin](../GDO/Admin/Module_Admin.php) (*optional*)
- [CLI](../GDO/CLI/Module_CLI.php) (*optional*)
- [**Core**](../GDO/Core/Module_Core.php)
- [Cronjob](../GDO/Cronjob/Module_Cronjob.php) (*optional*)
- [Crypto](../GDO/Crypto/Module_Crypto.php) (*optional*)
- [**Date**](../GDO/Date/Module_Date.php)
- DB (no module) (**required**)
- Form
- Install (not installable)
- Language
- Net
- Perf
- Table
- Tests (not installable)
- UI
- User
- Util (no module)

----

### GDOv7 core modules: Core

### GDOv7 core modules: Date

### GDOv7 core modules: F

### GDOv7 core modules: Date
