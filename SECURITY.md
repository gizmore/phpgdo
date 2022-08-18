# GDOv7 SECURITY.md

Security policy file for
[gdo6](https://github.com/gizmore/gdo6)
and
[GDOv7](https://github.com/gizmore/phpgdo).


## GDOv7 SECURITY.md: Ecosystem

There is currently only one developer who uses two factor auth and plaintext mail.


## GDOv7 SECURITY.md: Known vulnerabilities.

Some GDOv7 modules are currently vulnerable to package manager code injection via 3rd party package managers like yarn and composer.

@TODO Solution: Make sure that installed packages cannot be upgraded by pinning a version! - no upgrades for this lib then.

@TODO Solution: define protected/config.php:
"GDO_SEAL_PACKAGES" to give the user a choice.
Default this to true!


## Supported Versions

As no-one really uses this, it does not matter much.
There is only one branch, the master one, and all installations should be up to date, always.
Roughly you can say a security issue will patch all versions.

v7(properitary) is under development. v6(free) would still receive security fixes.

    +---------+-----------+
    | Version | Supported |
    +---------+-----+-----+
    | >=6.0.0 | Yes |
    | >=7.0.0 | Yes |
    +---------+-----+


## Reporting a Vulnerability

Just write me a mail (gizmore@wechall.net),
open an issue, or contact on irc.wechall.net.
A security problem will be fixed asap.
There is no real reward, but helping the GDO project is a hacking challenge flag on www.wechall.net :)


## Credits

Thanks for reading SECURITY.md!
 - gizmore


### Hall of Fame

 - [jusb3](https://www.wechall.net/profile/jusb3) (PaddingOracle attack on [phpgdo-session-cookie](https://github.com/gizmore/phpgdo-session-cookie)
 
 