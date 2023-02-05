<?php
namespace GDO\Install;

use GDO\Util\Filewalker;
use GDO\Core\GDT_Select;

/**
 * A select for a file in /phpgdo/DOCS/.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
final class GDT_DocsFile extends GDT_Select
{
	private static array $DOCS = []; 
	
	public function getChoices(): array
	{
		if (!self::$DOCS)
		{
			Filewalker::traverse(GDO_PATH . 'DOCS/', "/^_GDO7_/", [$this, '_buildEnum']);
		}
		return self::$DOCS;
	}

	/**
	 * Callback for docs traversal.
	 */
	public function _buildEnum(string $entry, string $fullpath): void
	{
		$key = substr($entry, 5, -3);
		self::$DOCS[$key] = $fullpath;
	}
	
	public function getDocsPath(): ?string
	{
		return $this->getValue();
	}
	
}
