# GDOv7 DBMS

GDOv7 DBMS are all handcrafted carefully
and should be faster than anything you have seen.
In GDOv7 there is not a single way to achieve things...
there *is*. **only**. **one**. **way**!

Since v7.0.2, a package providing the Module_DBMS
is a core dependencies and is **required**.

By featuring different DBMS, some additionaly gotchas
have been introduced to the DBAL.
In particular:

 - All *non-core* GDT have to be created from composite [Core/GDT](../GDO/Core), or else you need an ugly DBMS addition, to support custom create-code for all db systems.

 - No more `CONCAT()` available in SQL. Use `Module_DBMS::dbmsConcat()` to keep compatibility with all DBMS.
 
 - No more `FROM_UNIXTIME()` and `UNIX_TIMESTAMP()` available. Use `Module_DBMS::dbmsFromUnixtime()` and `Module_DBMS::dbmsTimestamp()` respectively.


## GDOv7 DBMS: MySQL/MariaDB

The
[MySQL variant](https://github.com/gizmore/phpgdo-mysql)
of Module_DBMS is the oldest and the default DBMS choice for an installation.

 - It implements all the features GDOv7 is offering.


## GDOv7 DBMS: SQLite3

The
[SQLite variant](https://github.com/gizmore/phpgdo-sqlite)
of Module_DBMS is available since 7.0.2.

The support was more or less experimental, to create a benchmark, but it seems stable now.

The gotchas and drawbacks of sqlite dbms are:

 - No support for session locking (experimental until this causes a problem.
 

## GDOv7 DBMS: Postgres (planned 7.0.3)

