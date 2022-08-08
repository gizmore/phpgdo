# GDOv7 SEO

We shall enforce aria and accessibility accross every component.
Javascript shall only enrich oldstyle components.
Automated tests shall point out weak titles and descriptions.


## GDOv7 SEO: SEO URLs

SEO urls are automagically generated.
It is planned to have an additional canoncial link mapper module.


## GDOv7 SEO: SEO URL generation

Example URL: /webroot/contact/form

Module and Method (mo/me) are identified via the first two path elements, module contact and method form.

From this point on, all append input parameters are put in pairs.

/webroot/contact/form/xsrf/abc12345

This will load input [xsrf]=abc12345 into the engine and is the same as ?xsrf=abc12345.

Parameters that start with an underscore are not part of the auto generated SEO urls.
Example parameters are _lang, _fmt and _ajax.

Dummy paramters are used to describe the content of the links.

For example /forum/thread/id/1414/page/4/t/What%20Shall%20I%20Do%23.html
