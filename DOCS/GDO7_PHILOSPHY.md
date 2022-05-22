# GDOv7 Philosphy

 - Everything is a string in the beginning. Do not convert unnecessarily and do it lazily. Sadly type annotations force a bit of conversion.

 - *No* code duplication. Re-use as much as possible. Validator inheritance and js bridge is noteworthy.

 - Less is more. there are no getters, just public attributes. Setters have the same name and allow chaining. This pattern is used across the whole project.
 
 - Aggressive programming, not defensive. Do not check for null values or anything. If there is a tiny problem, a helpful error has to be shown automatically!
 
 
## GDOv7 Philosphy: Public attribute pattern

Here is an example of how the code pattern above looks like.

    public string $label = 'test';
    public function label(string $label) : self
    {
        $this->label = $label;
        return $this;
    }


## GDOv7 Philosphy: Pluggable traits

GDT are plugged together with many traits.
A complete list of traits is available here.

 - [WithModule](../)
 - WithGDO
 - WithPHPJQuery
 
 
## GDOv7 Philosphy: Smart inheritance

I really rarely had to write some stupid validator for a long time.

## GDOv7 Philosphy: Coding guidelines


