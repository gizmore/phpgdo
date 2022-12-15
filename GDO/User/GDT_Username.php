<?php
namespace GDO\User;

use GDO\Core\GDT_Name;

/**
 * Username field without completion.
 * Can validate on existing, not-existing and both allowed (null)
 * 
 * @see GDT_User
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class GDT_Username extends GDT_Name
{
	const LENGTH = 32;
	
	public int $min = 2;
	public int $max = self::LENGTH;
	
	public string $icon = 'face';
	
	# Allow - _ LETTERS DIGITS
	public string $pattern = "/^[\\p{L}][-_\\p{L}0-9]+$/iuD";

	public function defaultLabel() : self { return $this->label('username'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->caseI();
	}
	
	##############
	### Exists ###
	##############
	public bool $exists;
	public function exists(bool $exists=true) : self
	{
		$this->exists = $exists;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		return isset($this->gdo) ? 
			$this->gdo->renderName() :
			$this->renderHTML();
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		
		# Check existance
		if (isset($this->exists) && ($this->exists === true))
		{
			if ($user = GDO_User::getByName($value))
			{
				$this->gdo = $user;
			}
			else
			{
				return $this->error('err_user');
			}
		}
		elseif (isset($this->exists) && ($this->exists === false))
		{
		    if ($user = GDO_User::getByName($value))
		    {
		        return $this->error('err_username_taken');
		    }
		}
		
		return true;
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => 'Lazer'],
		];
	}
	
}
