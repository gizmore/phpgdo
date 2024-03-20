<?php
declare(strict_types=1);
namespace GDO\Install;

use GDO\Core\GDT_Select;
use GDO\Util\Filewalker;

/**
 * A select for a file in /phpgdo/DOCS/.
 *
 * @version 7.0.3
 * @since 7.0.2
 * @author gizmore
 */
final class GDT_DocsFile extends GDT_Select
{

	private static array $DOCS = [];

	protected function getChoices(): array
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
        $n = count(self::$DOCS) + 1;
		self::$DOCS[$n] = $fullpath . "($n)";
	}

	public function getDocsPath(): ?string
	{
		return $this->getValue();
	}

}
