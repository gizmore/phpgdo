# GDOv7 Performance

Performance shall be O(N)!


## GDOv7 Performance: Headers

Watch out for the X-GDO-TIME and X-GDO-MEM headers.
A page should be around 25.0ms.

There is a metric called "func", which is number of functions called. [Module_Perf](../GDO/Perf/) is a core module that does report this metric beside others.

## GDOv7 Performance: Module_Perf

[Module_Perf](../GDO/Perf/) is a tiny core module that adds performance diagnostics. It is not enabled by default.


## GDOv7 Performance: HTTP/2.0

Performance enhancements that work in HTTP/1.1 should also affe HTTP/2.0. Else we won't care.
