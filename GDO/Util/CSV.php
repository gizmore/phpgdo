<?php
namespace GDO\Util;

/**
 * CSV Utilities
 *
 * @version 6.10
 * @since 6.10
 * @author gizmore
 */
final class CSV
{

	private string $path;
	private string $delimiter = ',';
	private string $enclosure = '"';
	private bool $withHeader = true;

	public function __construct($path)
	{
		$this->path = $path;
	}

    public static function parseGZLine($gz): ?array
    {
        $line = '';
        while ($chunk = gzgets($gz, 1048576))
        {
            $line .= $chunk;
            if ((substr_count($line, '"') % 2) === 0)
            {
                break;
            }
//            if (gzeof($gz))
//            {
//                break;
//            }
        }
        return $line ? str_getcsv($line) : null;
    }


    public function delimiter($delimiter): self
    {
		$this->delimiter = $delimiter;
		return $this;
	}

	public function enclosure($enclosure): self
    {
		$this->enclosure = $enclosure;
		return $this;
	}

	public function withHeader($withHeader = true): self
    {
		$this->withHeader = $withHeader;
		return $this;
	}

	public function eachLine($callable): void
    {
		if ($fh = @fopen($this->path, 'r'))
		{
			$first = $this->withHeader;
			while ($row = fgetcsv($fh, null, $this->delimiter, $this->enclosure))
			{
				if ($first)
				{
					$first = false;
				}
				else
				{
					$callable($row);
				}
			}
			fclose($fh);
		}
	}

	public function all(): array
    {
		$all = [];
		$fh = fopen($this->path, 'r');
		$first = $this->withHeader;
		while ($row = fgetcsv($fh, null, $this->delimiter, $this->enclosure, "\""))
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$all[] = $row;
			}
		}
		return $all;
	}

}
