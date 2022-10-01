<?php
namespace GDO\Net\Test;

use GDO\Net\GDT_PackedIP;
use GDO\Tests\TestCase;

final class IPTest extends TestCase
{
	public function testPackedIPv4() : void
	{
		$ip = '::1';
		$packed = GDT_PackedIP::ip2packed($ip);
		assertEquals("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x01", $packed, "Test if packed IPv6 encodes correctly.");
		$ip2 = GDT_PackedIP::packed2ip($packed);
		assertEquals('0000:0000:0000:0000:0000:0000:0000:0001', $ip2, "Test if packed IPv6 decodes correctly.");
		$this->assertOK("Test if IPv4 can be packed correctly");
	}
	
	public function testPackedIPv6() : void
	{
		$ip = '::1';
		$packed = GDT_PackedIP::ip2packed($ip);
		assertEquals("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x01", $packed, "Test if packed IPv6 encodes correctly.");
		$ip2 = GDT_PackedIP::packed2ip($packed);
		assertEquals('0000:0000:0000:0000:0000:0000:0000:0001', $ip2, "Test if packed IPv6 decodes correctly.");
		$this->assertOK("Test if IPv4 can be packed correctly");
	}
	
}
