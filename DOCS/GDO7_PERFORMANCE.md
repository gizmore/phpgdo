# GDOv7 Performance

Performance is one of the main reasons why i lost interest in many other frameworks.
The performance of GDOv7 is quite good already,
but I do not want to start optimizing too early.

While composer projects are still booting an optimized autoloader, GDOv7 might be doing heavy data work already.

I tried to compare roughly with Laravel8 and a recent Wordpress,
and both systems were beaten, even thought GDO had way more to load (empty laravel + wordpress, small gdo install iirc)

Real benchmarks are planned.

_In summary_: Wordpress is like 5 times slower and and memory hungry than gdo.
However, Laravel8 was only beaten marginally in memory and CPU footprint.


## GDOv7 Performance: Headers

Watch out for the X-GDO-TIME and X-GDO-MEM headers.

I am aiming for 5ms pages and 2MB ram on modest complex projects/pages.


## GDOv7 Performance: [GDT_PerfBar](../GDO/Perf/GDT_PerfBar.php)

There is a metric called "func",
which is number of functions called.
[Module_Perf](../GDO/Perf/)
is a core module that does report this metric beside others.

Here is an example CLI output of the GDT_PerfBar,
after running all unit tests on all modules.

    $ ./gdo_test.sh
    ######################################
    ### Welcome to the GDOv7 Testsuite ###
    ###       Enjoy your flight!       ###
    ######################################
    # LOTS OF AUTOMATED UNIT TESTS #
    ################################
    .
    .
    .
    .
    Finished with 1194 asserts after 39s.
    GDT_PerfBar->render() says:
    939 Log|8270 Qry|4005 Wr|5 Tr - |6.671s DB+32.514s PHP=39.186s - |68.00 MB|13200328 Func|5243 alloc - |1371 Classes|776 gdoClasses|4337(1962) GDT|65678(1531) GDO|100 mod|86 langfs
    - |610 tmpl|45 hook|0 ipc|1 mail - |199/1092 cache

What these values mean is discussed below.

## GDOv7 Performance: Module_Perf

[Module_Perf](../GDO/Perf/Module_Perf.php) is a tiny core module that adds performance diagnostics. It is not enabled by default.

The module is collecting metrics in
[GDT_PerfBar](../GDO/Perf/GDT_PerfBar.php)

- 939 Log - logfile lines have been written
- 8270 Qry - database queries total
- 4005 Wr - database writes
- 5 Tr - database transactions
- Timings and memory should be obvious
- 13200328 Func - function calls - needs xdebug
- 5243 Alloc - allocations, this is a bad estimation using spl_object_id(). This returns the max id, kinda stack max. It's a metric!
- @TODO List more GDT_Perf metrics

## GDOv7 Performance: HTTP/2.0

Performance enhancements that work in HTTP/1.1 should also affect HTTP/2.0. Else we should not care too much?
