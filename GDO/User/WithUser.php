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
    
    public function user(GDO_User $user) : self
    {
    	$this->user = $user;
    	return $this;
    }
    
    public function currentUser() : self
    {
    	return $this->user(GDO_User::current());
    }
    
    public function getUser() : GDO_User
    {
    	return $this->user;
    }

}
