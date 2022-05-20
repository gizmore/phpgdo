# GDOv7

Please note that this is under heavy work and API changes might happen. Still, the API will be quite compatible to [gdo6](https://github.com/gizmore/gdo6). Some links might not exist yet and some dates are pre-dated to the future.
The [changelog](GDO7_CHANGELOG.md) might be worth a read. Or maybe, if you are [combatible](GDO7_COMPATIBILITY.md), you might just want to 

    git clone gizmore/phpgdo # clone the code
    cd phpgdo # set it up by...
    php gdo_adm.php systemtest # Checking your compatibility
    php gdo_adm.php configure # writing a protected/config.php
    nano protected/config.php # edit your db settings. DB is required!
    php gdo_adm.php install_all # install all provided GDO_Modules
    php gdo_adm.php admin username password # add user to admins for bin/gdo
    # Add phpgdo/bin to your PATH environment variable.
    # É-voila, you can exec commands via gdo expression now.
    # Examples:
    gdo echo $(concat $(mul 3,4),monkeys)) # 12monkeys
    gdo mail.send gizmore,Hi there,$(concat Was geht du Nase?!, $(wget htts://google.de?q=phpgdo)) # This is like the goal of the gdo core now. It will send an email to gizmore without spoiling your mail address.
    
    #
    # Run full automagically unit tests thx to the GDT type system.
    #
    composer update
    ./gdo_test.sh # run like 150 asserts and maybe 75% code coverage completely auto generated.


Have fun!
-gizmore