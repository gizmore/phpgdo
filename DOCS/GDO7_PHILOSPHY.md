# GDOv7 Philosphy

 - Everything is a string in the beginning. Do not convert unnecessarily and do it lazily. 

 - *No* code duplication. Re-use as much as possible. Validator inheritance and js bridge is noteworthy.

 - Less is more. there are no getters, just public attributes. Setters have the same name and return $this for chaining. Example: function attr($attr) { $this->attr = $attr; return $this; }
 
 