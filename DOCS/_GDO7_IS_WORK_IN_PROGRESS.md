# GDOv7: Work in progress

Please note that this is heavily work in progress.
The API *might* change a bit here and there,
but it is still quite compatible to [gdo6](https://github.com/gizmore/gdo6).
Some links here might not exist yet.

The [changelog](GDO7_CHANGELOG.md) might be worth a read,
or maybe check if you are [compatible](GDO7_COMPATIBILITY.md),
or you might just want to...

    git clone --recursive gizmore/phpgdo # clone the code
    cd phpgdo
    
    php gdo_adm.php systemtest # Checking your compatibility
    php gdo_adm.php --interactive configure # writing a protected/config.php
    nano protected/config.php # finish the edit of config.php.
    gdo_adm.php test # test config.php
    
    php gdo_adm.php provide <module> # install a module
    php gdo_adm.php admin username password # add user to admins for bin/gdo
    php gdo_adm.php systemtest # Checking compatibility for all modules again.
 
    # Optional: Add phpgdo/bin to your PATH environment variable.
    # É-voila, you can exec commands via gdo cli expressions.
    # @examples:
    gdo echo $(concat $(mul 3,4), ,monkeys,!)) # 12 monkeys!
    gdo mail gizmo,Hi there,$(concat Was geht du Nase?!,$(wget htts://google.de?q=phpgdo))

## GDOv7: Test driven

phpgdo combines multiple strategies
to aid in improving code quality.

It is recommended to configure your installation to

- `GDO_ERROR_DIE=1` to treat reported errors as fatals.
  An error_reporting of `E_ALL` is recommended.

Run thousands of asserts,
which are mostly auto generated,
which seem to give quite a decent coverage.

This requires composer for phpunit tests.

    cd phpgdo
    composer update
    # Create a protected/config_test.php
    ./gdo_test.sh # Run all unit tests on all modules

Have fun!

-gizmore
