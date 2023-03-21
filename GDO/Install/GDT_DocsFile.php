<?php
namespace GDO\Install;

use GDO\Core\GDT_Select;
use GDO\Util\Filewalker;

/**
 * A select for a file in /phpgdo/DOCS/.
 *
 * @version 7.0.2
 * @since 7.0.2
 * @author gizmore
 */
final class GDT_DocsFile extends GDT_Select
{

	private static array $DOCS = [];

	public function getChoices(): array
	{
		if (!self::$DOCS)
		{
			Filewalker::traverse(GDO_PATH . 'DOCS/', '/^GDO7_/', [$this, '_buildEnum']);
		}
		return self::$DOCS;
	}

	/**
	 * Callback for docs traversal.
	 */
	public function _buildEnum(string $entry, string $fullpath): void
	{
		$key = substr($entry, 0, -3);
		self::$DOCS[$key] = $fullpath;
	}

	public function getDocsPath(): ?string
	{
		return $this->getValue();
	}

}
