<?php

function is_prime($n){for($i=$n**.5|1;$i&&$n%$i--;);return!$i&&$n>1;}

for ($i = 7; $i <= 1000; $i+=10)
{
	echo is_prime($i) ? '1' : '0';
}

echo "\n";
