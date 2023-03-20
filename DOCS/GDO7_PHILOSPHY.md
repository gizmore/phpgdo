# GDOv7 Philosophy

- Everything is a string in the beginning. Do not convert unnecessarily and do it lazily. Sadly type annotations force a bit of conversion.

- *No* code duplication. Re-use as much as possible. Validator inheritance and js bridge is noteworthy.

- *No* redundant information. I don't need to annotate in three ways that this is a string. Never is bad, one time is great, three times is sad.

- Consistency. Mostly, always?, There is one way to things. Do them this way and as good as you can.

- Less is more. there are no getters, just public attributes. Setters have the same name and allow chaining. This pattern is used across the whole project.

- Aggressive programming! not defensive. Do not check for null values or anything. If there is a problem, a helpful error has to be shown automatically!

- A method should fit on a screen. A file should have not more than 300 lines.
  Of course that's not always feasible,
  like [GDO.php](../GDO/Core/GDO.php)
  has almost 2000 lines of code.
  Those huge classes are very rare in GDOv7.

- Do things the right and easy way. *KISS\** :)
  If Something does not work right away, maybe come back later.
  Of course you have to try things first.

- No external resources unless really wanted. No production libraries during development, all src of everything.

## GDOv7 Philosophy: Public attribute pattern

Here is an example of how this code pattern looks like.

    public string $label = 'test';
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

This way you can chain setters like `GDT_String::make()->label('hi')->max(32)->ascii()->caseSensitive(true)`

## GDOv7 Philosophy: Pluggable traits

GDT are plugged together with many traits.
A complete list of traits is available here.

- [WithModule](../)
- WithGDO
- WithPHPJQuery

## GDOv7 Philosophy: Smart inheritance

I really rarely had to write some stupid validator for a long time.

## GDOv7 Philosophy: [GDT_Path]() and [GDT_Url]().

[FileUtil]()
[isFile()]() checks readability. (@TODO)

Directories always end with a trailing slash on all platforms.
I wished windows would switch to / as well -.-
