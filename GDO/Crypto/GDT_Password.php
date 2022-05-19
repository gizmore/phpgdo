<?php
namespace GDO\Crypto;

use GDO\Core\GDT_Template;
use GDO\Util\BCrypt;
use GDO\Core\GDT_String;

/**
 * Bcrypt hash form and database value.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0
 */
class GDT_Password extends GDT_String
{
    public function isSerializable() : bool { return true; }
    public function isSearchable() : bool { return false; }
    public function isOrderable() : bool { return false; }
    
    protected function __construct()
	{
        parent::__construct();
		$this->min = 59;
		$this->max = 60;
		$this->encoding = self::ASCII;
		$this->caseSensitive = true;
		$this->icon('lock');
		$this->tooltip('tt_password');
	}

	public function defaultLabel() : self { return $this->label('password'); }
	
	public function toValue($var)
	{
		return $var === null ? null : new BCrypt($var);
	}
	
	public function getGDOData() : ?array
	{
		$pass = $this->getValue();
		return [$this->name => $pass ? $pass->__toString() : null];
	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('User', 'form/password.php', ['field'=>$this]);
	}
	
	public function validate($value) : bool
	{
		if ($value === null || (!$value->hash))
		{
			return $this->notNull ? $this->errorNotNull() : true;
		}
		elseif (mb_strlen($value) < 4)
		{
			return $this->error('err_pass_too_short', [4]);
		}
		return true;
	}
	
	public function renderJSON()
	{
	}

}
