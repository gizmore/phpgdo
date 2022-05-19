<?php
namespace GDO\File;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_CreatedAt;
use GDO\User\GDO_User;

/**
 * Inherit from this table when using GDT_Files and provide your table to it.
 * Override gdoFileObjectTable() and return your GDO that shall have the files.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
class GDO_FileTable extends GDO
{
	################
	### Override ###
	################
	/**
	 * @return GDO
	 */
	public function gdoFileObjectTable() {}

	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoAbstract() : bool { return $this->gdoFileObjectTable() === null; }
	public function gdoColumns() : array
	{
		return array(
			GDT_AutoInc::make('files_id'),
			GDT_Object::make('files_object')->table($this->gdoFileObjectTable())->notNull(),
			GDT_File::make('files_file')->notNull(),
			GDT_CreatedBy::make('files_creator'),
			GDT_CreatedAt::make('files_created'),
		);
	}
	
	##############
	### Getter ###
	##############
	/**
	 * @return GDO_File
	 */
	public function getFile() { return $this->getValue('files_file'); }
	/**
	 * @return GDO_User
	 */
	public function getCreator() { return $this->getValue('files_creator'); }
	public function getCreatorID() { return $this->gdoVar('files_creator'); }
	
	###########
	### ACL ###
	###########
	public function canEdit(GDO_User $user) { return ($this->getCreatorID() === $user->getID()) || ($user->isStaff()); }
	public function canDelete(GDO_User $user) { return ($this->getCreatorID() === $user->getID()) || ($user->isStaff()); }

}
