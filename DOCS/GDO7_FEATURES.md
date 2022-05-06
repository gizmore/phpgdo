# GDOv7 Features

 - Write a method once for all sort of output. Be it CLI, HTML, JSON, AJAX, XML, etc. Re-Use the validation code nicely in a vast type hierarchy.
 
 - Write a GDT once and have perfect sanitization and validation on any user input. Re-use your GDT nicely.
 
 - Almost no 3rd party dependency in the core. Only [CSS Minifier](https://github.com/matthiasmullie/minify) and [HTML purifier](https://github.com/ezyang/htmlpurifier) to sanitize user html input. PHPUnit is installed via composer, if wanted / required.
 
 - Composer still not required nor wanted.
 
 - Blazing fast (4ms) for a PHP application with a good memory footprint (3MB). For some very simple pages that is.

 - Code driven database. Never write a single migration file again.

 - Consistent coding style and schema. Performance is a big aspect.
 
 - 2/3 Layer Single Identity Cache. Every Row is unique in memory, backed by memcached and a per process memory cache. An additional cache in the file system is also available. Caching that actually works, is easy to use, and actually speeds the application up.

 - Very clean code. In [EclipsePDT](https://www.eclipse.org/downloads/packages/release/2022-03/r/eclipse-ide-php-developers) there are almost no warnings. Other editors are, sadly, untested.
