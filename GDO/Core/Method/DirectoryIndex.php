<?php
namespace GDO\Core\Method;

use GDO\Core\GDO;
use GDO\Core\Module_Core;
use GDO\DB\ArrayResult;
use GDO\Table\MethodTable;
use GDO\Util\FileUtil;
use GDO\Core\GDO_DirectoryIndex;
use GDO\Net\GDT_Url;
use GDO\Util\Strings;

/**
 * Render a directory from the servers filesystem.
 * This can be disabled in Module_Core config.
 * 
 * @author gizmore
 *
 */
final class DirectoryIndex extends MethodTable
{
	public function isTrivial() : bool { return false; }
	
	public function isOrdered() : bool { return false; }
	public function isFiltered() { return false; }
	public function isSearched() { return false; }
	public function isPaginated() { return false; }
	
	public function plugVars() : array
	{
		return [
			'url' => 'GDO',
		];
	}
	
	public function gdoParameters() : array
	{
		return [
			GDT_Url::make('url')->allowInternal()->notNull(),
		];
	}
	
	public function getUrl() : string
	{
		$var = $this->gdoParameterVar('url');
		$var = ltrim($var, '/ ');
		return $var;
	}
	
	public function isAllowed() : bool
	{
		if (!Module_Core::instance()->cfgDirectoryIndex())
		{
			return false;
		}
		return $this->checkDotfile();
	}
	
	private function checkDotfile() : bool
	{
		if (Module_Core::instance()->cfgDotfiles())
		{
			return true;
		}
		return !$this->isDotFile($this->getUrl());
	}
	
	private function isDotFile(string $url) : bool
	{
		$filename = Strings::rsubstrFrom($url, '/', $url);
		if ($filename === '.well-known')
		{
			return false;
		}
		return $filename[0] === '.';
	}
	
	public function execute()
	{
		if (!$this->isAllowed())
		{
			return $this->error('err_method_disabled', [$this->getModuleName(), $this->getMethodName()]);
		}
		return parent::execute();
	}
	
	public function gdoTable() : GDO
	{
		return GDO_DirectoryIndex::table();
	}
	
	public function getTableTitle()
	{
		$count = $this->getResult()->numRows();
		return t('mt_dir_index', [html($this->getUrl()), $count]);
	}
	
	public function getMethodTitle() : string
	{
		return $this->getTableTitle();
	}
	
	public function getResult() : ArrayResult
	{
		$url = $this->getUrl();
		$data = [];
		$path = GDO_PATH . $url;
		$files = scandir($path);
		foreach ($files as $file)
		{
			if ($file === '.')
			{
				continue;
			}
			$path = "{$url}/{$file}";
			$data[] = $this->entry($path, $file);
		}
		return new ArrayResult($data, $this->gdoTable());
	}
	
	private function entry($path, $filename)
	{
		if (is_dir($path))
		{
			return GDO_DirectoryIndex::blank([
				'file_icon' => 'folder',
				'file_name' => $filename,
				'file_type' => 'directory',
				'file_size' => null, # @TODO Feature directory filesize in directory index.
			]);
		}
		else
		{
			return GDO_DirectoryIndex::blank([
				'file_icon' => 'file',
				'file_name' => $filename,
				'file_type' => FileUtil::mimetype($path),
				'file_size' => filesize($path),
			]);
		}
	}
}
