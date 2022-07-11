<?php
namespace GDO\User;

/**
 * GDT should have an associated user.
 * @author gizmore
 */
trait WithUser
{
	public GDO_User $user;
    public function user(GDO_User $user)
    {
    	$this->user = $user;
    	return $this;
    }
    
    public function currentUser()
    {
    	return $this->user(GDO_User::current());
    }

}
