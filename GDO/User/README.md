# phpgdo-user

Userobject and permission module for GDOv7.

## Profile main content

`User.Profile` calls `ProfileMainContent(GDT_Card $card, GDO_User $target,
GDO_User $viewer)` after the built-in settings. Enabled modules may add card
fields there. Each module must enforce its own per-viewer visibility rules.

## phpgdo-user Installation

This module is always installed.

### phpgdo-user License

This module is part of the Core and thus is GDOv7 Licensed.
