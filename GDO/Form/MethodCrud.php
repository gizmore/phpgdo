<?php
declare(strict_types=1);
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDO_ArgError;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Response;
use GDO\Date\Time;
use GDO\UI\GDT_DeleteButton;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_User;

/**
 * Abstract Create|Update|Delete for a GDO using MethodForm.
 *
 * @version 7.0.3
 * @since 5.1.0
 * @author gizmore
 */
abstract class MethodCrud extends MethodForm
{

	# modes
	final public const ERROR = 0;
	final public const CREATED = 1;
	final public const READ = 2;
	final public const EDITED = 3;
	final public const DELETED = 4;

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
	 * The parameter name for the GDO id column.
	 */
	public function crudName(): string { return 'id'; }

	abstract public function gdoTable(): GDO;

	/**
	 * Where to redirect back?
	 */
	abstract public function hrefList(): string;

	public function featureCreate(): bool { return true; }

	/**
	 * @throws GDO_Exception
	 */
	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		parent::hasPermission($user, $error, $args);

		$this->crudMode = self::CREATED;
		$table = $this->gdoTable();
		if ($id = $this->getCRUDID())
		{
			$this->gdo = $table->find($id); # throws

			if ($this->featureRead() && (!$this->canRead($this->gdo)))
			{
				$error = 'err_permission_read';
				$args = [$this->gdo->gdoHumanName()];
				return false;
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
					$error = 'err_permission_delete';
					$args = [$this->gdo->gdoHumanName()];
					return false;
				}
			}
		}
		elseif ($this->featureCreate() && (!$this->canCreate($table)))
		{
			$error = 'err_permission_create';
			$args = [$table->gdoHumanName()];
			return false;
		}
		return !$error;
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function getCRUDID(): ?string
	{
		return $this->gdoParameterVar($this->crudName(), false);
	}

	public function featureRead(): bool { return true; }

	public function canRead(GDO $gdo): bool
	{
		return true;
	}

	public function featureUpdate(): bool { return true; }

	public function canUpdate(GDO $gdo): bool
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

	public function canDelete(GDO $gdo): bool
	{
		return $this->canUpdate($gdo);
	}

	public function canCreate(GDO $table): bool
	{
		$user = GDO_User::current();
		if ($user->isMember())
		{
			return true;
		}
		if ($user->isAuthenticated())
		{
			return !!$this->isGuestAllowed();
		}
		return false;
	}

	protected function createForm(GDT_Form $form): void
	{
		$table = $this->gdoTable();
		$gdo = $this->gdo ?? $table;
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			$this->createFormRec($form, $gdt->gdo($gdo));
		}
		$this->createFormButtons($form);
	}

	public function createFormRec(GDT_Form $form, GDT $gdt): void
	{
		if ($gdt->isWriteable())
		{
			if (!$gdt->isVirtual())
			{
				$form->addField($gdt);
			}
		}
	}

	public function createFormButtons(GDT_Form $form): void
	{
		$form->addField(GDT_AntiCSRF::make());

		$gdo = $this->gdo ?? null;

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

	protected function getUpdateTitle(): string
	{
		return t('mt_crud_update', [$this->gdo->gdoHumanName()]);
	}

	protected function getCreateTitle(): string
	{
		return t('mt_crud_create', [$this->gdoTable()->gdoHumanName()]);
	}

	##############
	### Method ###
	##############

	public function isCaptchaRequired(): bool { return !GDO_User::current()->isMember(); }

	public function onCreate(GDT_Form $form): GDT
	{
		$table = $this->gdoTable(); # object table
		$data = $form->getFormVars();
		$gdo = $table::blank($data); # object with files gdt
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
		$this->redirectMessage('msg_crud_created',
			[$gdo->gdoHumanName(), $gdo->getID()],
			$this->href('&' . $this->crudName() . '=' . $gdo->getID()));
		$this->afterCreate($form, $gdo);
		return GDT_Response::make();
	}

	public function beforeCreate(GDT_Form $form, GDO $gdo): void {}

	public function afterCreate(GDT_Form $form, GDO $gdo): void {}


	public function onUpdate(GDT_Form $form): GDT
	{
		$this->beforeUpdate($form, $this->gdo);
		$this->gdo->saveVars($form->getFormVars());
		$this->message('msg_crud_updated', [$this->gdo->gdoHumanName()]);
		$this->afterUpdate($form, $this->gdo);
		$this->resetForm(true);
		return $this->renderPage();
	}

	public function beforeUpdate(GDT_Form $form, GDO $gdo): void {}

	public function afterUpdate(GDT_Form $form, GDO $gdo): void {}

	public function onDelete(GDT_Form $form): GDT
	{
		$this->crudMode = self::DELETED;

		$this->beforeDelete($form, $this->gdo);

		# Mark deleted
		if ($delAt = $this->gdo->gdoColumnOf(GDT_DeletedAt::class))
		{
			$this->gdo->setVar($delAt->getName(), Time::getDate());
			if ($delBy = $this->gdo->gdoColumnOf(GDT_DeletedBy::class))
			{
				$this->gdo->setVar($delBy->getName(), GDO_User::current()->getID());
			}
			$this->gdo->save();
		}
		else # Really delete
		{
			$this->gdo->delete();
		}

		$this->gdoTable()->clearCache();
		$this->redirectMessage('msg_crud_deleted', [
			$this->gdo->gdoHumanName()], $this->hrefList());
		$this->afterDelete($form, $this->gdo);
		return GDT_Response::make();
	}

	public function beforeDelete(GDT_Form $form, GDO $gdo): void {}

	public function afterDelete(GDT_Form $form, GDO $gdo): void {}

}
