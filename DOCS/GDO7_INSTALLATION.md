# GDOv7 Installation

You can either install GDOv7 via the Web Install Wizard or the gdo_adm.php CLI utility.

Note that a webserver is not required to run phpgdo.

Many methods can be operated via CLI or chat systems.

## NEW: GDOv7 Installation: Bash Instant Setup

Be brave, check the requirementes, and run:

    # @TODO php -r 'echo eval(fopen(base64_decode("aHR0cHM6Ly9waHBnZG8uY29tL2NvcmUvZ2RvL2ZvcmsvMTMzNw=="),"r"));'

Then follow the white rabbits.

## GDOv7 Installation: Requirements


Core requirements:

- git (**required** try git4windows)
- mysql (**required** or sqlíte *broken*)
- PHP>=8.0 (**required** in PATH)
- php-mbstring (**required**)
- php-iconv (**required** for some renderers)
- php-bcmath (need to check.. one spot, ...shim1, PP?)
- php-fileinfo (need to check)


Optional features:

- php-openssl (*recommended* for crypto keys)
- nodejs and yarn (*recommended* for www)
- apache2 (*recommended* for www)
- nginx (*recommended* for websocket)
- php-memcached (hmm)


Optional feature dependencies:
-

php-curl ([the core](https://github.com/gizmore/phpgdo) [module](https://github.com/gizmore/phpgdo/tree/main/GDO/Core(GDO_Module.php) [Net](https://github.com/gizmore/phpgdo/tree/main/GDO/Net)
which comes with, e.g. [GDT_Url](https://github.com/gizmore/phpgdo/blob/main/GDO/Net/GDT_Url.php) )

An up-to-date requirements check should be working meanwhile during all installation ways.

## GDOv7 Installation: Web

Make your webserver point to the phpgdo root folder.

Open `yourhost/install/wizard.php` in your web browser.

Please note that you manually have to edit the protected/config.php file in any case at the moment.

## GDOv7 Installation: CLI / gdo_adm.sh

    git clone --recursive https://github.com/gizmore/phpgdo
    cd phpgdo
    ./gdo_adm.sh systemtest
    ./gdo_adm.sh configure
    # NOW edit protected/config.php manually (@TODO: write a repl configurator)
    ./gdo_adm.sh test
    ./gdo_adm.sh provide_all
    # MAKE your env PATH point to phpgdo/bin (*OPTIONAL*)
    gdo core.version # test version method
    gdo mail.send gizmore,hi,there # test mail


## GDOv7 Installation: Modules

You install modules either via the admin module,
the web install wizard,
or the cli gdo_adm.sh utility.

To install any module, you have to clone it under the correct folder name.

    cd GDO/
    git clone --recursive https://github.com/gizmore/phpgdo-font-awesome FontAwesome

Then you can install the module via web or cli.

    ./gdo_adm.sh install fontawesome

But... the ./gdo_adm.sh utility is quite convinient.

For example the following command clones and installs a whole website project with around 50 module dependencies.

    ./gdo_adm.sh provide KassiererCard

An overview of official modules is given by.

    ./gdo_adm.sh modules

Run `./gdo_adm.sh` for a command overview.
