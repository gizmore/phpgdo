# GDOv7 Testing with Unit Tests

GDOv7 comes with an excellent test suite.
Tests are generated on GDT signatures, plugging default test values in.
This way a lot of code can be tested automatically.


## GDOv7 Unit Test Installation

To enable unit testing do as follows.

    cd phpgdo
    composer update
    # Create a protected/config_test.php
    ./gdo_test.sh # runs all tests
    ./gdo_test.sh <module> # runs test for all module dependencies


## GDOv7 Automated test case generation

To enable automated test generations and automatically test all GDT, GDO,
Method and utility,
install [Module_TestMethods](https://github.com/gizmore/phpgdo-test-methods),
Voilá, automagic tests enabled :)
