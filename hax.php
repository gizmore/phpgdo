<?php
echo "<pre>";
global $charset;
$charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

function charValue($char)
{
	global $charset;
	return stripos($charset, $char);
}

$cashkorken = [
	'KPH-ZXE7-33H',
	'ACM-TWJH-RZ4',
	'JH4-FNH4-GNR',
	'AXY-A7PC-DGB',
	'EE9-D4YN-BAT',
	'NTH-HBPF-DGD',
	'JND-9AHP-Z7F', # marvin@busch-peine.de : TheCrashKorken123!
	'PME-ZHHP-GKX', # marion@busch-peine.de : TheCrashKorken4!
	'YHX-NAED-GHT', # horst.jung@busch-peine.de : TheCrashKorken5!
	'MAA-GW7Z-7DW', # matthias.busch@busch-peine.de : TheCrashKorken6!
	// 	'INV-ALID-ATE',
// 	'INV-ALID-ATE',
// 	'NHP-WVXN-U6Y',
// 	'AAB-AAAC-AAD',
];

$invalid = [
	'NHP-WVXN-U6Y',
];


function cleankorken($korken)
{
	return str_replace('-', '', strtoupper($korken));
}

function korkensum($kork)
{
	$sum = 0;
	$korken = cleankorken($kork);
	$len = strlen($korken) - 1;
	for ($i = 0; $i < $len; $i++)
	{
		$sum += charValue($korken[$i]);
	}
	
	$checksum = charValue($korken[$len]);
	
	return sprintf("%s: Sum: %s; Checksum: %s; CheckChar: %s \n", $kork, $sum, $checksum, $korken[$len]);
	
}

function randomChar()
{
	global $charset;
	$min = 0;
	$max = strlen($charset) - 1;
	return $charset[rand($min, $max)];
}

function korkenChar($i)
{
	global $charset;
	return $charset[$i];
}

function checksumChar($korken)
{
	$sum = 0;
	$korken = cleankorken($korken);
	$len = strlen($korken) - 1;
	for ($i = 0; $i < $len; $i++)
	{
		$sum += charValue($korken[$i]);
	}
	return korkenChar($sum % 36);
}

function generateCashKorken()
{
	global $charset;
	
	$korken = '';
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= '-';
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= '-';
	$korken .= randomChar();
	$korken .= randomChar();
	$korken .= checksumChar($korken);

	return "$korken\n";
}

function sumOver($korken, $start, $end)
{
	$korken = cleankorken($korken);
	$sum = 0;
	for ($i = $start; $i < $end; $i++)
	{
		$sum += charValue($korken[$i]);
	}
	return $sum;
}

function sumFirstBlock($korken)
{
	return sumOver($korken, 0, 3);
}

function sumSecondBlock($korken)
{
	return sumOver($korken, 3, 7);
}

function sumThirdBlock($korken)
{
	return sumOver($korken, 7, 10);
}

function sumLastTwo($korken)
{
	return sumOver($korken, 7, 9);
}

foreach ($cashkorken as $korken)
{
	echo korkensum($korken);
}

foreach ($cashkorken as $korken)
{
	printf("%s: SUM1: %3s %3s %3s;    SUM2: %3s %3s %3s %3s;    SUM3: %3s %3s\n",
		$korken,
	sumOver($korken, 0, 1),
	sumOver($korken, 0, 2),
	sumOver($korken, 0, 3),
	
	sumOver($korken, 3, 4),
	sumOver($korken, 3, 5),
	sumOver($korken, 3, 6),
	sumOver($korken, 3, 7),
	
	sumOver($korken, 7, 8),
	sumOver($korken, 7, 9),
	);
}

foreach ($cashkorken as $korken)
{
	printf("%s: SUMMIES: %3s %3s %3s %3s %3s %3s %3s %3s %3s\n",
	$korken,
	sumOver($korken, 0, 1),
	sumOver($korken, 0, 2),
	sumOver($korken, 0, 3),
	
	sumOver($korken, 0, 4),
	sumOver($korken, 0, 5),
	sumOver($korken, 0, 6),
	sumOver($korken, 0, 7),
	
	sumOver($korken, 0, 8),
	sumOver($korken, 0, 9),
	);
}

echo generateCashKorken();

