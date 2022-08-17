<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDO_PermissionException;
use GDO\UI\GDT_DeleteButton;
use GDO\User\GDO_User;
use GDO\Core\GDT_Object;
use GDO\Core\GDT;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Date\Time;
use GDO\Util\Common;
use GDO\Core\GDT_CreatedBy;
use GDO\UI\GDT_EditButton;

/**
 * Abstract Create|Update|Delete for a GDO using MethodForm.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.1.0
 */
abstract class MethodCrud extends MethodForm
{
    # modes
	const ERROR = 0;
	const CREATED = 1;
	const READ = 2;
	const EDITED = 3;
	const DELETED = 4;
	
	protected int $crudMode = self::ERROR;

	protected GDO $gdo;

	public abstract function gdoTable() : GDO;
	
	/**
	 * Where to redirect back?
	 */
	public abstract function hrefList() : string;
	
	################
	### Override ###
	################
	public function isUserRequired() : bool { return true; }
	public function isCaptchaRequired() { return !GDO_User::current()->isMember(); }
	public function isShownInSitemap() : bool { return false; }
	
	public function canRead(GDO $gdo)
	{
	    return true;
	}
	
	public function canCreate(GDO $table)
	{
	    $user = GDO_User::current();
	    if ($user->isMember())
	    {
	        return true;
	    }
	    if ($user->isAuthenticated())
	    {
	        return $this->isGuestAllowed();
	    }
	    return false;
	}
	
	public function canUpdate(GDO $gdo)
	{
	    $user = GDO_User::current();
	    if ($gdt = $gdo->gdoColumnOf(GDT_CreatedBy::class))
	    {
	        if ($user === $gdt->getValue())
	        {
	            return true;
	        }
	    }
	    if ($user->isStaff())
	    {
	        return true;
	    }
	    return false;
	}
	
	public function canDelete(GDO $gdo)
	{
	    return $this->canUpdate($gdo);
	}
	
	public function beforeCreate(GDT_Form $form, GDO $gdo) {}
	public function beforeUpdate(GDT_Form $form, GDO $gdo) {}
	public function beforeDelete(GDT_Form $form, GDO $gdo) {}
	
	public function afterCreate(GDT_Form $form, GDO $gdo) {}
	public function afterUpdate(GDT_Form $form, GDO $gdo) {}
	public function afterDelete(GDT_Form $form, GDO $gdo) {}

	/**
	 * The parameter name for the GDO id column(s)
	 * @return string
	 */
	public function crudName() { return 'id'; }
	public function getCRUDID()
	{
	    return Common::getRequestString($this->crudName());
	}

	##############
	### Method ###
	##############
	public function gdoParameters() : array
	{
	    $p = [
	        GDT_Object::make($this->crudName())->table($this->gdoTable()),
	    ];
	    return array_merge($p, parent::gdoParameters());
	}
	
	public function onInit()
	{
		parent::onInit();
	    $this->crudMode = self::CREATED;
	    $table = $this->gdoTable();
	    if ($id = $this->getCRUDID())
	    {
	        $this->gdo = $table->find($id); # throws
	        $this->crudMode = self::EDITED;
	        if (!$this->canRead($this->gdo))
	        {
	            throw new GDO_PermissionException('err_permission_read');
	        }
	        elseif (!$this->canUpdate($this->gdo))
	        {
	            $this->crudMode = self::READ;
	        }
	        else
	        {
	            $this->crudMode = self::EDITED;
	        }
	    }
	    elseif (!$this->canCreate($table))
	    {
	    	return $this->error('err_permission_create,' [$table->gdoHumanName()]);
// 	        throw new GDO_PermissionException('err_permission_create', $this, $);
	    }
	    
	    $this->getForm();
	}
	
	##############
	### Create ###
	##############
	public function createForm(GDT_Form $form) : void
	{
	    $table = $this->gdoTable();
	    if (isset($this->gdo))
	    {
	    	$form->gdo($this->gdo);
	    }
	    foreach ($table->gdoColumnsCache() as $gdt)
	    {
		    $gdo = isset($this->gdo) ? $this->gdo : $table;
	        $this->createFormRec($form, $gdt->gdo($gdo));
		}
// 		$this->createCaptcha($form);
		$this->createFormButtons($form);
	}
	
	public function createFormRec(GDT_Form $form, GDT $gdt) : void
	{
		if ($gdt->isWriteable())
		{
// 	        $gdt->writeable = $this->crudMode !== self::READ;
			if (!$gdt->isVirtual())
			{
			    $form->addField($gdt);
			}
		}
	}

// 	public function createCaptcha(GDT_Form $form)
// 	{
// 		if (module_enabled('Captcha'))
// 		{
// 			if ($this->isCaptchaRequired())
// 			{
// 				$form->addField(GDT_Captcha::make());
// 			}
// 		}
// 	}
	
	public function createFormButtons(GDT_Form $form) : void
	{
// 		$form->addField(GDT_AntiCSRF::make());
		
		$gdo = isset($this->gdo) ? $this->gdo : null;
		
		if (!$gdo)
		{
			$c = GDT_Submit::make('create')->label('btn_create')->icon('create')->onclick([$this, 'onCreate']);
		    $form->actions()->addField($c);
		}

		if ($gdo && $this->canUpdate($this->gdo))
		{
			$u = GDT_EditButton::make('edit')->label('btn_edit')->icon('edit')->onclick([$this, 'onUpdate']);
    		$form->actions()->addField($u);
		}

		if ($gdo && $this->canDelete($this->gdo))
		{
			$d = GDT_DeleteButton::make()->onclick([$this, 'onDelete']);
			$form->actions()->addField($d);
		}

// 		if ($gdo)
// 		{
//     	    $form->withGDOValuesFrom($this->gdo);
// 		}
// 		else
// 		{
// 		    $form->withGDOValuesFrom($this->gdoTable());
// 		}
	}
	
	public function getMethodTitle() : string
	{
		return isset($this->gdo) ? $this->getUpdateTitle() : $this->getCreateTitle();
	}
	
	protected function getCreateTitle()
	{
		return t('mt_crud_create', [$this->gdoTable()->gdoHumanName()]);
	}
	
	protected function getUpdateTitle()
	{
        return t('mt_crud_update', [$this->gdo->gdoHumanName()]);
	}
	
	##############
	### Bridge ###
	##############
	public function formValidated(GDT_Form $form)
	{
		return $this->renderPage();
	}

	public function onSubmit_create(GDT_Form $form)
	{
	    return $this->onCreate($form);
	}
	
	public function onSubmit_edit(GDT_Form $form)
	{
	    return $this->onUpdate($form);
	}
	
	public function onSubmit_delete(GDT_Form $form)
	{
		return $this->onDelete($form);
	}
	
	####################
	### CRUD Actions ###
	####################
	public function onCreate(GDT_Form $form)
	{
		$table = $this->gdoTable(); # object table
		$data = $form->getFormVars();
		$gdo = $table->blank($data); # object with files gdt
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
        $this->redirectMessage('msg_crud_created',
            [$gdo->gdoHumanName(), $gdo->getID()],
            $this->href('&'.$this->crudName().'='.$gdo->getID()));
        return $this->afterCreate($form, $gdo);
	}
	
	public function onUpdate(GDT_Form $form)
	{
	    $this->beforeUpdate($form, $this->gdo);
		$this->gdo->saveVars($form->getFormVars());
		$this->message('msg_crud_updated', [$this->gdo->gdoHumanName()]);
		return $this->afterUpdate($form, $this->gdo);
	}
	
	public function onDelete(GDT_Form $form)
	{
		$this->crudMode = self::DELETED;
		
		$this->beforeDelete($form, $this->gdo);
		
		# Mark deleted
		if ($delAt = $this->gdo->gdoColumnOf(GDT_DeletedAt::class))
		{
		    $this->gdo->setVar($delAt->name, Time::getDate());
		    if ($delBy = $this->gdo->gdoColumnOf(GDT_DeletedBy::class))
		    {
		        $this->gdo->setVar($delBy->name, GDO_User::current()->getID());
		    }
		    $this->gdo->save();
		}
		else # Really delete
		{
    		$this->gdo->delete();
		}
		
		$this->gdo->table()->clearCache();
		$this->messageRedirect('msg_crud_deleted', [$this->gdo->gdoHumanName()], $this->hrefList());
		return $this->afterDelete($form, $this->gdo);
	}
	
}
