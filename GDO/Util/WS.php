<?php
declare(strict_types=1);
namespace GDO\Util;

use GDO\Core\Application;

/**
 * Websocket i/o utility
 *
 * @version 7.0.3
 */
final class WS
{

	public function writeFloat($float) { return self::wrF($float); }

	public static function wrF($float) { return pack('f', floatval($float)); }

	public function writeDouble($double) { return self::wrD($double); }

	public static function wrD($double) { return pack('d', doubleval($double)); }

	public function writeTimestamp() { return self::wrTS(); }

	public static function wrTS() { return self::wr32(Application::$TIME); }

	public static function wr32($value) { return self::wrN(4, $value); }

	public function write8($value) { return self::wrN(1, $value); }

	public function writeCmd($value) { return self::wrN(2, $value); }

	public function write32($value) { return self::wrN(4, $value); }

	###############
	### Hexdump ###
	###############

	public function write64($value) { return self::wrN(8, $value); }

}
