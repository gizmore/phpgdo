<?php
chdir(__DIR__);
require 'protected/config.php';
require 'GDO7.php';

/**
 * Update the official provider cache.
 */

# OLD
$filename = GDO_PATH . 'GDO/Core/ModuleProviders.php';
$file = file_get_contents($filename);

#################
### Providers ###
#################
$output = [];
$return_var = 0;
$out = exec('php providers.php --for_gizmore >> out.txt 2>&1 &', $output, $return_var);
if ($return_var != 0)
{
	echo $out;
	die($return_var);
}
$deps = implode("\n", $output);
$bd = '### BEGIN_PROVIDERS ###';
$ed = '### END_PROVIDERS ###';
$file = preg_replace("/{$bd}.*{$ed}/s", "{$bd}\n{$deps}\n{$ed}", $file);

############
### Deps ###
############
$output = [];
$return_var = 0;
$out = exec('php provider_dependencies.php --for_gizmore >> out.txt 2>&1 &', $output, $return_var);
if ($return_var != 0)
{
	echo $out;
	die($return_var);
}
$deps = implode("\n", $output);
$bd = '### BEGIN_DEPENDENCIES ###';
$ed = '### END_DEPENDENCIES ###';
$file = preg_replace("/{$bd}.*{$ed}/s", "{$bd}\n{$deps}\n{$ed}", $file);

##############
### Output ###
##############
echo $file;
file_put_contents($filename, $file);
die(0);
