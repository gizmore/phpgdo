<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\Application;
use GDO\Core\GDO_FileCache;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\Date\Time;
use GDO\DB\Cache;
use GDO\Net\GDT_Url;
use GDO\Util\FileUtil;

/**
 * Serve a static file from the webserver.
 * Might be forbidden if it's an asset from the /GDO/ folder.
 * Might be forbidden if it's a dotfile.
 *
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 */
final class Fileserver extends Method
{

	public function isHiddenMethod(): bool { return false; }

	public function isTrivial(): bool { return false; }

	public function isAlwaysAllowed(): bool { return true; }

	public function gdoParameters(): array
	{
		return [
			GDT_Url::make('url')->allowExternal(false),
		];
	}

	public function execute(): GDT
	{
		$url = $this->gdoParameterVar('url', false);
		$url = ltrim($url, '/');

		# Deny by asset rule?
		GDT_Hook::callHook('BeforeServeAsset', $url);

		if (Application::isError())
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}

		if (!Module_Core::instance()->checkAssetAllowed($url))
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}

		if (!$this->checkDotfileAllowed($url))
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}

// 		if (Application::isError())
// 		{
// 			return NotAllowed::make()->executeWithInputs($this->inputs);
// 		}

		# Deny PHP source
		$type = FileUtil::mimetype($url);
		if ($type === 'text/x-php')
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}

		# Try cached or serve
		$mtime = filemtime($url);
		$etag = $this->md5_file($url, $mtime);
		hdr('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
		hdr("Etag: $etag");
		hdr('Expires: ' . gmdate('D, d M Y H:i:s', $mtime + Time::ONE_MONTH) . ' GMT');

		$app = Application::$INSTANCE;

		$mtime2 = @$_SERVER['HTTP_IF_MODIFIED_SINCE'] ?: Time::getDate();

		# cache hit
		if (
			(@strtotime($mtime2) < $mtime) ||
			(trim((string)@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
		)
		{
			hdrc('HTTP/1.1 304 Not Modified');
			$app->timingHeader();
			die(0);
		}

		# 200 - serve
		hdr('Content-Type: ' . $type);
		hdr('Content-Size: ' . filesize($url));
		$app->timingHeader();
		readfile($url);
		die(0);
	}

	/**
	 * Check if an URL is an allowed resource.
	 * Dotfiles may be forbidden, except .well-known
	 */
	public function checkDotfileAllowed(string $url): bool
	{
		if (Module_Core::instance()->cfgDotfiles())
		{
			# All allowed by config
			return true;
		}
		foreach (explode('/', $url) as $segment)
		{
			# special dotfile allowed
			if ($segment === '.well-known')
			{
				return true;
			}
			# other dotfile forbidden
			if ($segment[0] === '.')
			{
				return false;
			}
		}
		# no dotfile
		return true;
	}

	/**
	 * @TODO: implement an md5 cache for the fs.
	 */
	private function md5_file(string $path, int $last_modified_time): string
	{
		$key = 'FSMD5.' . str_replace('/', '.', $path) . '.' . $last_modified_time;
		if (null === ($md5 = Cache::fileGet($key)))
		{
			$md5 = GDO_FileCache::md5For($path, $last_modified_time);
			Cache::fileSet($key, $md5);
		}
		return $md5;
	}

}
