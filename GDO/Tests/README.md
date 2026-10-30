# phpgdo-tests

Unit test helper module for GDOv7
Launches quite early some basic test setup.
Test users are getting created as soon as possilbe (Module_Language, priority 2, directly after Module_Core).
This is a core module.

It also generates tests automagically for all trivial pluggable methods.

From the PHPGDO root, `./gdo_test.sh <module> [filter]` runs the selected
module's tests; the optional second argument is a literal test-name substring.
Filtering also runs the Language setup tests that create test users and select
English; a filter matching no tests fails instead of passing on setup alone.
An explicitly selected utility module, such as `./gdo_test.sh ui`, runs its
own suite. Use `--utility` to include utility suites when running a broader
selection such as `--all`.
The test runner rebuilds its configured test database before running tests.
