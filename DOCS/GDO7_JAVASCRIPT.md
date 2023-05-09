# GDOv7 Javascript

This is a guideline and documentation for Javascript in GDOv7.

## GDOv7 Javascript: Asset Pipeline

GDOv7 does not have an asset pipeline.
You press F5 to deliver the latest assets,
which are the files you work on.

For real production builds, use the
[Javascript](https://github.com/gizmore/phpgdo-javascript)
module.
This is still just pressing *F5*,
but **all** javascript files get minified,
obfuscated and cached on the fly.

All JS source files are forbidden to be delivered, when you use this module.
Which is way better as what other frameworks do *by design* to aid you.

By the way, the same process, and F5 toolchain, also exists for
[CSS](https://github.com/gizmore/phpgdo-css).


## GDOv7 Javascript: Guideline


Always make things work without JS, as we want to use gdo methods on the CLI/Chat as well. Use JS *only* to enhance the experience in the web.
