# GDOv7: Work in progress

Please note that this is heavily work in progress.
The API might change a bit here and there,
but it will be quite compatible to [gdo6](https://github.com/gizmore/gdo6).
Some links might not exist yet and some dates are pre-dated to the future.
The [changelog](GDO7_CHANGELOG.md) might be worth a read.
Or maybe, if you are [combatible](GDO7_COMPATIBILITY.md), you might just want to...

    git clone --recursive gizmore/phpgdo # clone the code
    cd phpgdo
    
    php gdo_adm.php systemtest # Checking your compatibility
    php gdo_adm.php configure # writing a protected/config.php
    nano protected/config.php # edit your db settings. DB is required!
    
    php gdo_adm.php install_all # install all provided GDO_Modules
    php gdo_adm.php admin username password # add user to admins for bin/gdo
    
    # Add phpgdo/bin to your PATH environment variable.
    # É-voila, you can exec commands via gdo expression now.
    # Examples:
    gdo echo $(concat $(mul 3,4),monkeys)) # 12monkeys
    gdo mail.send gizmore,Hi there,$(concat Was geht du Nase?!,$(wget htts://google.de?q=phpgdo))
    # This is like the goal of the gdo core now.
    # It will send an email to gizmore without spoiling your mail address.
    # Appending a fetched website.


## GDOv7: Test driven

Run about 215 asserts which are mostly auto generated and cover 85% code. This requires composer.

    cd phpgdo
    composer update
    # Create a protected/config_test.php
    ./gdo_test.sh # Run all unit tests on all modules
    

Have fun!

-gizmore
