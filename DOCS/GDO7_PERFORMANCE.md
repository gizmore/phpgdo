# GDOv7 Performance

Performance shall be O(N)! :D

Of course performance is one of the main reasons i lost interest in many other frameworks.
The performance of GDOv7 is quite good already,
but I do not want to start optimizing too early.


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
    
    
    
    
## GDOv7 Performance: Module_Perf

[Module_Perf](../GDO/Perf/Module_Perf.php) is a tiny core module that adds performance diagnostics. It is not enabled by default.


## GDOv7 Performance: HTTP/2.0

Performance enhancements that work in HTTP/1.1 should also affect HTTP/2.0. Else we should not care too much?
