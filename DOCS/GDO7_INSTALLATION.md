# GDOv7 Installation

You can either install GDOv7 via the Web Install Wizard or the gdo_adm.php CLI utility.
Note that a webserver is not required to operate GDOv7.
All methods can be operated via CLI or chat systems.


## GDOv7 Installation: Web

Make your webserver point to the phpgdo root folder.
Open `yourhost/install/index.php` in your web browser.


## GDOv7 Installation: CLI

    git clone --recursive https://github.com/gizmore/phpgdo
    cd phpgdo
    ./gdo_adm.sh systemtest
    ./gdo_adm.sh configure
    # NOW edit protected/config.php manually (@TODO: write a repl configurator)
    ./gdo_adm.sh install_all
    # MAKE your PATH point to phpgdo/bin
    gdo core.version # test version method


## GDOv7 Installation: Web Modules

## GDOv7 Installation: CLI Modules

