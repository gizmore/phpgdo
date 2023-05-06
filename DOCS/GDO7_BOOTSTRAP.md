# GDOv7 Bootstrap

The GDOv7 application framework uses itself to install.

The bootstrap process:

    @include protected/config.php
    require GDO7.php
    Application::init();


That's it. phpgdo should be useable.


# GDOv7 Bootstrap: Init

Of course GDOv7 is humble. It does not do anything when bootstrapped.

Try to enable the logger, debugger and render something.

    Debug::init(true, true) # init debugger with die and mail.
    Logger::init('testuser', 0xffffffff); # all log levels
    echo GDT_String::make()->initial("Hello world")->render();
