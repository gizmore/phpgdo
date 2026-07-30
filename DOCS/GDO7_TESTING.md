# GDOv7 Testing

GDOv7 comes with an excellent test suite.
Tests are generated on GDT signatures, plugging default test values in.
This way a lot of code can be tested automatically.
Of course some handwritten tests are priceless,
but it is encouraging, give it a try:


## GDOv7 Testing: Quickstart

To enable unit testing do as follows.

    cd phpgdo
    composer install
    # Create protected/config_test.php with a dedicated test database.
    ./gdo_test.sh # runs all modules and enabled test options
    ./gdo_test.sh Tests # runs the Tests module and its dependencies
    ./gdo_test.sh --quick '%' # quick run over all modules

The test runner **drops and recreates** the database named in
`protected/config_test.php`, clears `files_test/`, and removes the configured
temporary directory. Never point it at a development or production database.

The configured test database user must be able to connect to MySQL/MariaDB and
create, drop, and use that dedicated database. Test options must come before
the module selector; for example, use `./gdo_test.sh --quick '%'`, not
`./gdo_test.sh '%' --quick`.

## GDOv7 Automated test case generation

The GDOv7 Type System allows to automatically test a lot of methods and their paramters.

There is a Test that fuzzes all Methods:
[AutomatedMethodTest](../GDO/Tests/Test/AutomatedMethodTest.php)

There is a Test that fuzzes all rendering of all GDT+GDO:
[AutomatedRenderingTest](../GDO/Tests/Test/AutomatedRenderingTest.php)
