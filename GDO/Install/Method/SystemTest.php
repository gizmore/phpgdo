<?php
namespace GDO\Install\Method;

use GDO\Core\Method;
use GDO\Util\FileUtil;
use GDO\CLI\Process;

/**
 * Do some tests and output in page.
 * 
 * @author gizmore
 * @version 7.0.2
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
		$tVars = [
			'tests' => [
				$this->testPHPVersion(),
				FileUtil::createDir(GDO_PATH . 'protected'),
				FileUtil::createDir(GDO_PATH . 'files'),
				FileUtil::createDir(GDO_PATH . 'temp'),
			    FileUtil::createDir(GDO_PATH . 'assets'),
				function_exists('mb_strlen'),
			    function_exists('mime_content_type'),
				function_exists('bcadd'),
				function_exists('iconv'),
			],
			'optional' => [
			    function_exists('curl_init'),
			    function_exists('imagecreate'),
			    class_exists('\\Memcached', false),
				function_exists('openssl_cipher_iv_length'),
				$this->testYarn(),
			],
		];
		return $this->templatePHP('page/systemtest.php', $tVars);
	}
	
	private function testPHPVersion()
	{
		$version = floatval(PHP_MAJOR_VERSION. '.' . PHP_MINOR_VERSION);
		return $version >= 8.0;
	}
	
	private function testYarn(): bool
	{
		return !!Process::commandPath('yarn');
	}
	
}
