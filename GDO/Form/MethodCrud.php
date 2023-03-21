<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDO_CRUDException;
use GDO\Core\GDO_PermissionException;
use GDO\Core\GDT;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Object;
use GDO\Date\Time;
use GDO\UI\GDT_DeleteButton;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_User;

/**
 * Abstract Create|Update|Delete for a GDO using MethodForm.
 *
 * @version 7.0.1
 * @since 5.1.0
 * @author gizmore
 */
abstract class MethodCrud extends MethodForm
{

	# modes
	public const ERROR = 0;
	public const CREATED = 1;
	public const READ = 2;
	public const EDITED = 3;
	public const DELETED = 4;

	protected int $crudMode = self::ERROR;

	protected GDO $gdo;

	public function isUserRequired(): bool { return true; }

	public function isShownInSitemap(): bool { return false; }

	################
	### Override ###
	################

	public function gdoParameters(): array
	{
		$p = [
			GDT_Object::make($this->crudName())->table($this->gdoTable())->notNull(!$this->featureCreate()),
		];
		return array_merge($p, parent::gdoParameters());
	}

	/**
	 * The parameter name for the GDO id column(s)
	 *
	 * @return string
	 */
	public function crudName() { return 'id'; }

	abstract public function gdoTable(): GDO;

	public function featureCreate(): bool { return true; }

	/**
	 * @throws GDO_PermissionException
	 */
	public function onMethodInit()
	{
		parent::onMethodInit();
		$this->crudMode = self::CREATED;
		$table = $this->gdoTable();
		if ($id = $this->getCRUDID())
		{
			$this->gdo = $table->find($id); # throws

			if ($this->featureRead() && (!$this->canRead($this->gdo)))
			{
				throw new GDO_CRUDException('err_permission_read');
			}

			if ($this->featureUpdate() && (!$this->canUpdate($this->gdo)))
			{
				$this->crudMode = self::READ;
			}
			else
			{
				$this->crudMode = self::EDITED;
			}

			if ($this->featureDelete() && $this->hasInputFor('delete'))
			{
				if (!$this->canDelete($this->gdo))
				{
					throw new GDO_CRUDException('err_permission_delete', [$this->gdo->gdoHumanName()]);
				}
			}
		}
		elseif ($this->featureCreate() && (!$this->canCreate($table)))
		{
			return $this->error('err_permission_create', [$table->gdoHumanName()]);
		}

		$this->resetForm();
	}

	public function getCRUDID(): ?string
	{
		return $this->gdoParameterVar($this->crudName(), true, false);
	}

	public function featureRead(): bool { return true; }

	public function canRead(GDO $gdo)
	{
		return true;
	}

	public function featureUpdate(): bool { return true; }

	public function canUpdate(GDO $gdo)
	{
		$user = GDO_User::current();
		if ($user->isStaff())
		{
			return true;
		}
		if ($gdt = $gdo->gdoColumnOf(GDT_CreatedBy::class))
		{
			if ($user === $gdt->getValue())
			{
				return true;
			}
		}
		return false;
	}

	public function featureDelete(): bool { return true; }

	public function canDelete(GDO $gdo)
	{
		return $this->canUpdate($gdo);
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

	public function createForm(GDT_Form $form): void
	{
		$table = $this->gdoTable();
// 	    if (isset($this->gdo))
// 	    {
// 	    	$form->initFromGDO($this->gdo);
// 	    }
		$gdo = isset($this->gdo) ? $this->gdo : $table;
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			$this->createFormRec($form, $gdt->gdo($gdo));
		}
// 		$this->createCaptcha($form);
		$this->createFormButtons($form);
	}

	public function createFormRec(GDT_Form $form, GDT $gdt): void
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

	public function createFormButtons(GDT_Form $form): void
	{
		$form->addField(GDT_AntiCSRF::make());

		$gdo = isset($this->gdo) ? $this->gdo : null;

		if ((!$gdo) && ($this->featureCreate()))
		{
			$c = GDT_Submit::make('create')->label('btn_create')->icon('create')->onclick([$this, 'onCreate']);
			$form->actions()->addField($c);
		}

		if ($gdo && $this->canUpdate($this->gdo) && $this->featureUpdate())
		{
			$u = GDT_EditButton::make('edit')->label('btn_edit')->icon('edit')->onclick([$this, 'onUpdate']);
			$form->actions()->addField($u);
		}

		if ($gdo && $this->canDelete($this->gdo) && $this->featureDelete())
		{
			$d = GDT_DeleteButton::make()->onclick([$this, 'onDelete']);
			$form->actions()->addField($d);
		}
	}

	public function getMethodTitle(): string
	{
		return isset($this->gdo) ? $this->getUpdateTitle() : $this->getCreateTitle();
	}

	protected function getUpdateTitle()
	{
		return t('mt_crud_update', [$this->gdo->gdoHumanName()]);
	}

	protected function getCreateTitle()
	{
		return t('mt_crud_create', [$this->gdoTable()->gdoHumanName()]);
	}

	##############
	### Method ###
	##############

	public function isCaptchaRequired() { return !GDO_User::current()->isMember(); }

	public function onCreate(GDT_Form $form)
	{
		$table = $this->gdoTable(); # object table
		$data = $form->getFormVars();
		$gdo = $table->blank($data); # object with files gdt
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
		$this->redirectMessage('msg_crud_created',
			[$gdo->gdoHumanName(), $gdo->getID()],
			$this->href('&' . $this->crudName() . '=' . $gdo->getID()));
		return $this->afterCreate($form, $gdo);
	}

	##############
	### Create ###
	##############

	public function beforeCreate(GDT_Form $form, GDO $gdo) {}

	public function afterCreate(GDT_Form $form, GDO $gdo) {}

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

	public function onUpdate(GDT_Form $form)
	{
		$this->beforeUpdate($form, $this->gdo);
		$this->gdo->saveVars($form->getFormVars());
		$this->message('msg_crud_updated', [$this->gdo->gdoHumanName()]);
		$this->afterUpdate($form, $this->gdo);
		$this->resetForm(true);
		return $this->renderPage();
	}

	public function beforeUpdate(GDT_Form $form, GDO $gdo) {}

	public function afterUpdate(GDT_Form $form, GDO $gdo) {}

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
		$this->redirectMessage('msg_crud_deleted', [
			$this->gdo->gdoHumanName()], $this->hrefList());
		return $this->afterDelete($form, $this->gdo);
	}

	####################
	### CRUD Actions ###
	####################

	public function beforeDelete(GDT_Form $form, GDO $gdo) {}

	/**
	 * Where to redirect back?
	 */
	abstract public function hrefList(): string;

	public function afterDelete(GDT_Form $form, GDO $gdo) {}

}
