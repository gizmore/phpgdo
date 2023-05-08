# GDOv7: Kernel Configuration

Kernel Configuration is done by including a `protected/config.php`,
before it bootstraps by including [GDO7.php](../GDO7.php).

A config.php is re-written, validated and upgraded by the following actions.

- `gdo_adm.sh configure` (when using the `--force` or no `protected/config.php` exists)
- `gdo_adm.sh confgrade` (always)
- `gdo_fix.sh` (always)
- `gdo_update.sh` (when Module_Core::GDO_REVISION changes)

The known configuration variables are documented in
[Module_Install](../GDO/Install/Module_Install.php):
[\GDO\Install\Config.php](../GDO/Install/Config.php#L200),
which is used by the install/wizard.php gdo application.

The `install/wizard.php` is a website,
using phpgdo to manage the `protected/config.php`.
When a database connection is possible,
[GDO_Module](../GDO/Core/GDO_Module.php)s
can be installed.
A GDO_Module will install any [GDO](../GDO/Core/GDO.php) returned in `getClasses()`. @TODO Rename to maybe gdoTableNames().
A GDO is just a containered special [GDT](../GDO/Core/GDT.php).
GDT means gizmore data type, the types of which everything is plugged together.
GDTs know how to behave in several situtations.

[Validation](./GDO7_VALIDATION.md) -
[Rendering](./GDO7_RESPONSE_FORMATS.md) -
[Configuration](./GDO7_CONFIG.md#gdov7-module-configuration) -
[Testing](./GDO7_TESTING.md)


# GDOv7: Module Configuration

Modules can define their configration variables
by overriding `getConfig()`, returning an array of GDT.

Changes to these GDT are made by either
[Module_Admin](../GDO/Admin/Module_Admin.php),
`./gdo_adm.sh config <module> <name> <var>`
