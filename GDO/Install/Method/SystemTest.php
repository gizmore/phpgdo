<?php
namespace GDO\Install\Method;

use GDO\Core\Method;
use GDO\Util\FileUtil;

/**
 * Do some tests and output in page.
 * @author gizmore
 */
final class SystemTest extends Method
{
	public function getMethodTitle() : string
	{
		return t('install_title_2');
	}
	
	public function getMethodDescription() : string
	{
		return t('install_title_2');
	}
	
	public function execute()
	{
		$tVars = array(
			'tests' => array(
				$this->testPHPVersion(),
				FileUtil::createDir(GDO_PATH . 'protected'),
				FileUtil::createDir(GDO_PATH . 'temp'),
			    FileUtil::createDir(GDO_PATH . 'assets'),
				$this->testYarn(),
				function_exists('mb_strlen'),
			    function_exists('mime_content_type'),
				function_exists('bcadd'),
			),
			'optional' => array(
			    function_exists('curl_init'),
			    function_exists('imagecreate'),
			    class_exists('\\Memcached', false),
				function_exists('openssl_cipher_iv_length'),
			),
		);
		return $this->templatePHP('page/systemtest.php', $tVars);
	}
	
	private function testPHPVersion()
	{
		$version = floatval(PHP_MAJOR_VERSION. '.' . PHP_MINOR_VERSION);
		return $version >= 8.0;
	}

	private function testYarn() : bool
	{
		return true;
	}
	
}
