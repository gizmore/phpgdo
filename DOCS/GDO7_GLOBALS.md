# GDOv7 Globals

There are some useful [global functions](../GDO7.php) defined.

- $me - Current [Method](../GDO/Core/Method.php) to execute.

- t($key, $args) - global I18n

- html($s) - html escape (unless cli)

- json($s) - json escape (for json and html within html attributes or something)

- escape($s) - database escaping

- quote($s) - database quoting "escape()"

- href($module, $method, $append) - Build a HREF, probably with SEO rules.
