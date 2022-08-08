<?php
namespace GDO\User;

use GDO\DB\Query;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;

/**
 * An autocomplete enabled user field.
 * 
 * Settings:
 * - ghost(): fallback to ghost user for null
 * - fallbackCurrentUser(): fallback to current user for null
 * - withPermission(): only allow users with a certain permission
 * - withType(): only allow users of a certain type
 * 
 * @TODO: rename fallbackCurrentUser()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_User extends GDT_Object
{
	public function defaultLabel() : self { return $this->label('user'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->orderField = 'user_name';
		$this->table(GDO_User::table());
		$this->icon('face');
        $this->withCompletion();
	}

	public function withCompletion()
	{
		return $this->completionHref(href('User', 'Completion', '&_fmt=json'));
	}
	
	#############
	### Ghost ###
	#############
	private bool $ghost = false;
	public function ghost(bool $ghost=true) : self
	{
		$this->ghost = $ghost;
		return $this;
	}
	
	###############
	### Current ###
	###############
	public bool $fallbackCurrentUser = false;
	public function fallbackCurrentUser(bool $fallbackCurrentUser=true) : self
	{
	    $this->fallbackCurrentUser = $fallbackCurrentUser;
	    return $this;
	}
	
	############
	### Type ###
	############
	public string $withType;
	public function withType(string $withType) : self
	{
	    $this->withType = $withType;
	    return $this;
	}
	
	############
	### Perm ###
	############
	public string $withPermission;
	public function withPermission(string $withPermission) : self
	{
	    $this->withPermission = $withPermission;
	    return $this;
	}
	
	#############
	### Value ###
	#############
	/**
	 * Get selected user.
	 */
	public function getUser() : ?GDO_User
	{
		return $this->getValue();
	}
	
	/**
	 * @return GDO_User
	 */
	public function getValue()
	{
		if ($user = parent::getValue())
		{
		    return $user;
		}
		if ($this->fallbackCurrentUser)
		{
		    return GDO_User::current();
		}
		if ($this->ghost)
		{
			return GDO_User::ghost();
		}
	}

// 	public function findByName($name)
// 	{
// 		if (str_starts_with($name, GDO_User::GHOST_NAME_PREFIX))
// 		{
// 			return null;
// 		}
// // 		elseif (str_starts_with($name, GDO_User::REAL_NAME_PREFIX))
// // 		{
// // 			return GDO_User::table()->findBy('user_real_name', trim($name, GDO_User::REAL_NAME_PREFIX.GDO_User::REAL_NAME_POSTFIX));
// // 		}
// 		elseif (str_starts_with($name, GDO_User::GUEST_NAME_PREFIX))
// 		{
// 			return GDO_User::table()->findBy('user_guest_name', trim($name, GDO_User::GUEST_NAME_PREFIX));
// 		}
// 		else
// 		{
// 			return GDO_User::getByName($name);
// 		}
// 	}
	
	protected function getGDOsByName(string $var): array
	{
		$field = 'user_name';
		$p = GDO_User::GUEST_NAME_PREFIX;
		if ($var[0] === $p)
		{
			$field = 'user_guest_name';
		}
		$var = GDO::escapeSearchS(trim($var, "$p \t\r\n"));
		$query = GDO_User::table()->select()->
			where("{$field} LIKE '%{$var}%'")->
			limit(GDT_Object::MAX_SUGGESTIONS);
		return $query->exec()->fetchAllObjects();
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
	    /** @var $user GDO_User **/
	    $user = $value;
	    
	    if (!parent::validate($value))
	    {
	        return false;
	    }
	    
	    if ($value === null)
	    {
	        return true; # Null check passed already
	    }
	    
	    if (isset($this->withType))
	    {
	        if ($user->getType() !== $this->withType)
	        {
	            $typelabel = t('enum_' . $this->withType);
	            return $this->error('err_user_type', [$typelabel]);
	        }
	    }
	    
	    if (isset($this->withPermission))
	    {
	        if (!$user->hasPermission($this->withPermission))
	        {
	            $permlabel = t('perm_' . $this->withPermission);
	            return $this->error('err_user_no_permission', [$permlabel]);
	        }
	    }
	    
	    return true;
	}
	
	public function plugVar() : string
	{
	    return '2'; # gizmore in unit tests.
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
	    if ($user = $this->getUser())
	    {
	        return $user->renderUserName();
	    }
	    return t('unknown');
	}
	
	public function renderJSON()
	{
	    return $this->renderCell();
	}
	
	##############
	### Filter ###
	##############
	public $noFilter = false;
	public function noFilter($noFilter=true)
	{
		$this->noFilter = $noFilter;
		return $this;
	}
	
	public function filterQuery(Query $query, $rq=null) : self
	{
		if (!$this->noFilter)
		{
			if ($filter = $this->filterVar($rq))
			{
				$filter = GDO::escapeSearchS($filter);
				$filter = "LIKE '%{$filter}%'";
				$this->filterQueryCondition($query,
				    "user_name $filter OR user_guest_name $filter OR user_real_name $filter");
			}
		}
		return $this;
	}
	
	###########
	### CLI ###
	###########
	public function gdoExampleVars() : ?string
	{
		return 'giz|tehr|liv|d';
	}
	
}
