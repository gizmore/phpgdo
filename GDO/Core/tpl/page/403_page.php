<?php
namespace GDO\Core\tpl\page;
use GDO\UI\GDT_Error;
?>
<h1>NoNoNo!!</h1>

<p>Haxor haben hier nix verloren!</p>

<p>In case you think this is an error. It is not.</p>

<!-- giz -->

<?php
echo GDT_Error::make()->code(403)
	->title('forbidden')
	->text('err_forbidden')
	->render();
