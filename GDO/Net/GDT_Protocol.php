<?php
namespace GDO\Net;

use GDO\Core\GDT_Select;

final class GDT_Protocol extends GDT_Select
{

	public const HTTP = 'http';
	public const HTTPS = 'https';
	public const SSH = 'ssh';
	public const IRC = 'irc';
	public const IRCS = 'ircs';
	public const FTP = 'ftp';
	public const FTPS = 'ftps';
	public const SFTP = 'sftp';
	public const TCP = 'tcp';
	public const TCPS = 'tcps';
	public const RDP = 'rdp';

	###########
	### GDT ###
	###########
	public $protocols = [];

	public function allowHTTP()
	{
		return $this->allowProtocols('http', 'https');
	}

	public function allowProtocols(string ...$protocols)
	{
		$this->protocols = array_unique(array_merge($this->protocols, $protocols));
		$this->protocols = array_combine($this->protocols, $this->protocols);
		return $this;
	}

	public function allowProtocol(string $protocol, bool $allow = true)
	{
		if ($allow)
		{
			$this->protocols[$protocol] = $protocol;
		}
		else
		{
			unset($this->protocols[$protocol]);
		}
		return $this;
	}

	###############
	### Choices ###
	###############
	public function getChoices(): array
	{
		$choices = $this->emptyVar === null ? [] :
			[$this->emptyVar => $this->displayEmptyLabel()];
		$choices = array_merge($choices, $this->protocols);
		return $choices;
	}

	##############
	### Render ###
	##############
	public function render(): array|string|null
	{
		$this->initChoices();
		return parent::render();
	}

	################
	### Validate ###
	################
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		$this->initChoices();
		return parent::validate($value);
	}

}
