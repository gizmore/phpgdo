# GDOv7: Configuration

Configuration is done by including a `protected/config.php`.

The config.php is re-generated and upgraded on:

- `gdo_fix.sh` (always)
- `gdo_adm.sh confgrade` (always)
- `gdo_update.sh` (only version change)
- `gdo_adm.sh configure` (only with `--force` parameter)

The known configuration variables are documented in
[Module_Install](../GDO/Install/Module_Install.php):
[\GDO\Install\Config.php](../GDO/Install/Config.php#L200)

@TODO: Include a multi-user custom config.php.

To bootstrap phpgdo, just include
[GDO7.php](../GDO7.php).
