# GDOv7 Testing with Unit Tests

GDOv7 comes with an excellent test suite.
Tests are generated on GDT signatures, plugging default test values in.
This way a lot of code can be tested automatically.

## GDOv7 Unit Test Installation

To enable unit testing do as follows.

    cd phpgdo
    composer update
    # Create a protected/config_test.php - This can be a copy of your config.php, but it should have an own database.
    ./gdo_test.sh # runs all tests
    ./gdo_test.sh <module> # runs test for a single module and all it's dependencies.

## GDOv7 Automated test case generation

The GDOv7 Type System allows to automatically test a lot of methods and their paramters.

There is a Test that fuzzes all Methods:
[AutomatedTest](../GDO/Tests/Test/AutomatedTest.php)

There is a Test that fuzzes all rendering of all GDT+GDO:
[AutomatedRenderingTest](../GDO/Tests/Test/AutomatedRenderingTest.php)
