<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Name;
use GDO\UI\GDT_EditButton;
use GDO\Language\Trans;
use GDO\Core\GDT_Virtual;
use GDO\Core\GDT_UInt;

/**
 * Permission entities.
 * One of the first GDO that use GDT_Virtual, for usercount.
 * 
 * @version 7.0.0
 * @since 3.1.0
 * @author gizmore
 * @see GDT_Virtual
 */
final class GDO_Permission extends GDO
{
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('perm_id'),
			GDT_Name::make('perm_name')->notNull(),
		    GDT_Level::make('perm_level')->notNull(false),
		    GDT_Virtual::make('perm_usercount')->gdtType(GDT_UInt::make())->label('user_count')->
		        subquery("SELECT COUNT(*) FROM gdo_userpermission WHERE perm_perm_id = perm_id"),
		];
	}
	
	public static function getByName(string $name) : ?self { return self::getBy('perm_name', $name); }
	
	public static function getOrCreateByName(string $name, string $level='0') : self { return self::create($name, $level); }
	
	public static function create(string $name, string $level='0') : self
	{
		if (!($perm = self::getByName($name)))
		{
			$perm = self::blank(['perm_name' => $name, 'perm_level' => $level])->insert();
		}
		elseif ($perm->getLevel() != $level)
		{
		    # Fix level because install method makes sure the permission exists.
		    if ($perm->getLevel() == '0')
		    {
    		    $perm->saveVar('perm_level', $level);
		    }
		}
		return $perm;
	}
	
	##############
	### Getter ###
	##############
	public function getName() : ?string { return $this->gdoVar('perm_name'); }
	public function getLevel() : ?string { return $this->gdoVar('perm_level'); }
	
	###############
	### Display ###
	###############
	public function renderName() : string
	{
	    $name = $this->getName();
	    $key = 'perm_' . $name;
	    if (Trans::hasKey($key))
	    {
	    	return t($key);
	    }
	    return $name ? $name : t('unknown_permission');
	}
	
	public function display_perm_edit() { return GDT_EditButton::make()->href($this->hrefEdit()); }
	public function display_user_count() { return $this->gdoVar('user_count'); }
	public function renderOption() : string { return sprintf('%s–%s', $this->getID(), $this->renderName()); }
	
	############
	### HREF ###
	############
	public function href_edit()
	{
		return href('Admin', 'ViewPermission', '&permission='.$this->getID());
	}
	
}
