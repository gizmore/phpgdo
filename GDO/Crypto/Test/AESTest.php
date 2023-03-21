<?php
namespace GDO\Crypto\Test;

use GDO\Crypto\AES;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

/**
 * AES testsuite.
 *
 * @author gizmore
 */
final class AESTest extends TestCase
{

	public function testAES()
	{
		$password = 'Lump';
		$plaintext = 'Test1234!';

		$encrypted = AES::encryptIV($plaintext, $password);
		assertTrue($plaintext !== $encrypted, 'Test if we can AES encrypt.');

		$decrypted = AES::decryptIV($encrypted, $password);
		assertTrue($plaintext === $decrypted, 'Test if we can AES decrypt.');
	}

}
