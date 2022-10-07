<?php
namespace GDO\Net\Test;

use GDO\Net\GDT_PackedIP;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

final class IPTest extends TestCase
{
	public function testPackedIP() : void
	{
		$ip = '::1';
		$packed = GDT_PackedIP::ip2packed($ip);
		assertEquals("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x01", $packed, "Test if packed IPv6 encodes correctly.");
		$ip2 = GDT_PackedIP::packed2ip($packed);
		assertEquals('::1', $ip2, "Test if packed IPv6 decodes correctly.");
		
		$ip = '127.0.0.1';
		$packed = GDT_PackedIP::ip2packed($ip);
		assertEquals("\x7f\0\0\x01", $packed, "Test if IPv4 packs correctly.");
		$unpacked = GDT_PackedIP::packed2ip($packed);
		assertEquals($ip, $unpacked, 'Test if IPv4 unpacks correctly again.');
	}
	
}
