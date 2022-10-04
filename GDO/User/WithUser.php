<?php
namespace GDO\User;

/**
 * GDT should have an associated user.
 * 
 * @author gizmore
 */
trait WithUser
{
	public GDO_User $user;
	
    public function user(?GDO_User $user) : self
    {
    	if ($user === null)
    	{
    		unset($this->user);
    	}
    	else
    	{
    		$this->user = $user;
    	}
    	return $this;
    }
    
    ###############
//     ### Current ###
//     ###############
//     public bool $fallbackCurrentUser = false;
//     public function fallbackCurrentUser(bool $fallbackCurrentUser=true) : self
//     {
//     	$this->fallbackCurrentUser = $fallbackCurrentUser;
//     	return $this;
//     }
    
//     public function currentUser() : self
//     {
//     	return $this->user(GDO_User::current());
//     }
    
    public function getUser() : ?GDO_User
    {
    	return isset($this->user) ? $this->user : GDO_User::current();
    }

}
