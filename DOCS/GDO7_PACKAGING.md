## GDOv7 Packaging

Package management and module handling the GDOv7 way.

GDOv7 does **NOT** use composer for package management.

But,
There is composer used in [unit tests](GDO7_TESTING.md).
And there are some modules that use [WithComposer](../GDO/Core/WithComposer.php) to wrap or use other people's work...

composer is not *required*.

Instead we use git to install and update GDOv7 modules.
On windows, please use [git4windows](https://gitforwindows.org/) - it is great!


## GDOv7 Packaging; psr-4

GDOv7 should be psr-4 compliant.
It has an own minimal and [optimized autoloader](../GDO7.php#L23) in 5 lines of code.
The composer.json should work and allows to require GDOv7 in your own composer projects.
This is **forbidden** by it's [GDOv7-LICENSE](../LICENSE)!


## GDOv7 Packaging: Providers

GDOv7 has the ability to exchange modules for another.
An example is [Module_Session](https://github.com/gizmore/phpgdo-session-cookie). 

Here, i tricked you.
There are actually two Module_Sessios.

[Module_Session] 
 - [Module_Session](https://github.com/gizmore/phpgdo-session-cookie) (via cookie)
 - [Module_Session](https://github.com/gizmore/phpgdo-session-db) (via database)

This is called providing.
There is no extra code for this mechanism.
This just seems to be a simple,
and a natural and elegant way, of handling packages. :)


## GDOv7 Packaging: Module Installation

Short story:

    ./gdo_adm.sh provide <module>
	
	
See a list of modules: [GDO7_MODULES.md](GDO7_MODULES.md)

    ./gdo_adm.sh modules


Longer story:

The installation process for a GDOv7 module is as follows.

Clone the module into your GDO folder with the correct foldername.
The correct foldername is important.
You should always use --recursive when you clone a GDOv7 repository.
This step is the "provider" thingy :)

        cd phpgdo/GDO # go to module folder
        git clone --recursive https://github.com/gizmore/phpgdo-jquery JQuery # clone this module as JQuery
	
	
Then you install the module. `./gdo_adm.sh install JQuery`.

This does install the module to the database, and does some work like triggering hooks and using package managers to download more software.

Please note that GDOv7 always uses the sourcecode for a javascript dependency in debug mode.
For production the asset pipeline is fully F5,
 any source is denied by htaccess.

To reproduce the single steps, this is a good start:

		
		./gdo_adm.sh install <module>
        ./gdo_yarn.sh # Install yarn dependencies
		./gdo_post_install.sh # trigger post install hooks
		enable the module # HOW?!
    

That's it.
Your application now includes the latest jQuery assets via src.

 
## Updating your installation

Updating your GDOv7 modules can be easily done using the following command.

    /phpgdo/gdo_update.sh
	
	
This will run git pull on the core and all modules.
Afterwards `./gdo_adm.sh upgrade` is executed.
This re-installs and upgrades all modules.

The developers shall assure that all modules stay backwards and forward compatible!
Luckily there is only one stupid developer.

