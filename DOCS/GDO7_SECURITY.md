# GDOv7 SECURITY

I am glad you made it here.
Security is important.
You don't want those bloody lamerz to ruin your work.
GDOv7 does it's best to protect your GDO ecosystem...

[SECURITY.md](./SECURITY.md)


## GDOv7 SECURITY: PHP

PHP changed a lot the past years and got more secure defaults.
However GDOv7 does a nasty trick.
Every little issue, warning or notice causes the debugger to crash the application with a log entry, an email and a rollback.
User input can only enter the ecosystem if it's validated from their GDT.
404 Pages can optionally cause an email, which is nice to find dead links or spot an attack.
Of course nowadays the web is a much more safe place than back in 2000.
Sane cookie defaults and other measures are of course also in place.
The passwords use bcrypt as their hash,
and some salt and pepper to keep the GPUs busy.
Every request should and can be piped through GDO to enforce ACL.


## GDOv7 SECURITY: Javascript




## GDOv7 SECURITY: Dependencies


