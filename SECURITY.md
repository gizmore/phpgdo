# GDOv7 SECURITY.md

Security Policy file of GDOv7.


## GDOv7 SECURITY.md: Known vulnerabilities.

Some GDOv7 modules are currently vulnerable to package manager code injection via 3rd party package managers like yarn, composer, and more?

Solution: Make sure that installed packages cannot be upgraded. by pinning a version! - no upgrades :(

Solution: define protected/config.php:
"GDO_SEAL_PACKAGES" to give the user a choice.
Default this to true!


## Supported Versions

As no-one really uses this, it does not matter much.
There is only one branch, the master one, and all installations should be up to date, always.
Roughly you can say a security issue will patch all versions.

New: v7 is under development, But v6 will still receive security fixes.

| Version | Supported          |
| ------- | ------------------ |
| >=6.0.0 | Yes |
| >=7.0.0 | Yes |


## Reporting a Vulnerability

Just write me a mail (gizmore@wechall.net), open an issue, or contact on irc.wechall.net.
A security problem will be fixed asap.
There is no real reward, but helping the GDO project is a flag on a hacking challenge on wechall.net :)


## Credits

Thanks for reading SECURITY.md!
 - gizmore
