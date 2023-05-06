# GDOv7 Debugging

In this page i want to explain the debugging techniques available in GDOv7.

## GDOv7 Debugging: Core/Debug

The [Debug.php](../GDO/Core/Debug.php)
class enables an own error handler,
which is sending an error mail to configured recipients.

Error messages are written to `STDERR`.

Javascript errors are similiar and trigger an GDOv7 Method to send E-Mails, also on client errors.

This is quite useful, but *can* also cause mail havoc on frequented hosts. Handle with care.


## GDOv7 Debugging: Methods

[Methods](../GDO/Core/Method.php)
can be easily debugged by overriding `isDebugging()`.
This triggers a few breakpoints in the execution process,
precisely, before init, validation, and execution.


## GDOv7 Debugging: Database

We all want to know what the raw queries are.

The easiest to debug a query is set a
[Query](../GDO/DB/Query.php)
into `->debug()` mode.
This will print the raw query to stdout on execution.

If you want to log all queries, you can enable GDO_DB_DEBUG in your protected/config.php.

To enable Query logging set GDO_DB_DEBUG to 1 or 2.

If set to 2, every query is logged with an additional backtrace.
This can help to figure out where db queries originate.

## GDOv7 Debugging: Allocations

To enable GDO/GDT allocation logging set GDO_GDT_DEBUG to 1 or 2.

If set to 2, GDO does log every GDT/GDO allocation with an additional backtrace.

This can help to track down mass allocations.

## GDOv7 Debugging: xDebug

Of course you should make use of a debugger.
I use [xdebug](https://pecl.php.net/package/xdebug).
In [Eclipse PDT](https://www.eclipse.org/pdt/),
i have xDebug configured and can set breakpoints to step the application at any time.
This works from bash as well as from chrome.
I do not know what i did without a debugger for *sooo* long.

Here is an example php.ini for xdebug:

    zend_extension="c:/wamp64/bin/php/php8.1.8/zend_ext/php_xdebug-3.1.5-8.1-vs16-x86_64.dll"
    xdebug.mode = develop,debug,profile # profile is optional.
    xdebug.client_port = 9003
    xdebug.start_with_request = yes
    xdebug.discover_client_host = true
    xdebug.output_dir ="C:\_Portable\qcachegrind074-x86"
    xdebug.profiler_output_name=callgrind.cli.%H.%t.%p.cgrind

### GDOv7 Debugging: Javascript

To debug JS code, simply place the word `debugger;` anywhere in your JS Sources.
It will trigger the debugger in chrome.

Also, gdo comes without any binary blob.
For every library, the sourcecode distribution is loaded.

On production sites it is recommended to install
[Javascript](https://github.com/gizmore/phpgdo-javascript)
and
[CSS](https://github.com/gizmore/phpgdo-css)
to create minfied asset builds on the fly,
also blocking all source files so your scripts are safe.

## GDOv7 Debugging: Performance

You might be interested in the
[Performance](GDO7_PERFORMANCE.md)
chapter to read about profiling and other performance measures.
