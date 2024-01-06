<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_EditedAt;
use GDO\Core\GDT_EditedBy;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\Core\WithGDO;
use GDO\Date\GDT_DateDisplay;
use GDO\Form\WithActions;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;
use GDO\User\GDT_User;
use GDO\User\WithAvatar;

/**
 * A card with title, subtitle, creator, date, content and actions.
 *
 * @version 7.0.3
 * @since 6.0.4
 * @author gizmore
 */
class GDT_Card extends GDT
{

	use WithGDO;
	use WithTitle;
	use WithSubTitle;
	use WithImage;
	use WithAvatar;
	use WithFields;
	use WithActions;
	use WithPHPJQuery;

	public GDT $content;

	###############
	### Content ###
	###############
	public GDT $image;
	public GDT $footer;

	#############
	### Image ###
	#############

	public function isTestable(): bool
	{
		return false;
	}

	public function content($content): self
	{
		$this->content = $content;
		return $this;
	}

	##############
	### Footer ###
	##############

	public function image(GDT $image): self
	{
		$this->image = $image;
		return $this;
	}

	public function footer(?GDT $footer): self
	{
		if ($footer)
		{
			$this->footer = $footer;
		}
		else
		{
			unset($this->footer);
		}
		return $this;
	}

	##############
	### Render ###
	##############

	/**
	 * Use the subtitle to render creation stats.
	 * User (with avatar), Date, Ago.
	 * You can override the ago subtitle.
	 * If you do not want anything, just don't use this method.
	 */
	public function creatorHeader(string $byField = null, string $atField = null, string $subtitleOverride = null, bool $subtitleNoUser = false): self
	{
		if ($byField)
		{
			$byField = $this->gdo->gdoColumn($byField);
		}
		else
		{
			$byField = $this->gdo->gdoColumnOf(GDT_CreatedBy::class);
		}
		if (!$byField)
		{
			$byField = $this->gdo->gdoColumnOf(GDT_User::class);
		}

		/** @var GDO_User $user * */
		$user = $byField?->getValue();

		if ($atField)
		{
			$atField = $this->gdo->gdoColumn($atField);
		}
		else
		{
			$atField = $this->gdo->gdoColumnOf(GDT_CreatedAt::class);
		}

		# Add avatar
		$this->avatarUser($user, 52);

		# Add created by / at to subtitle
		if ($subtitleOverride)
		{
			$this->subtitleRaw($subtitleOverride);
		}
		else
		{
			$profileLink = GDT_ProfileLink::make()->user($user)->nickname()->level();
			$date = t('unknown');
			if ($atField)
			{
				$date = GDT_DateDisplay::make($atField->getName())->gdo($this->gdo)->render();
			}
			$this->subtitle('creator_header', [$profileLink->render(), $date]);
		}
		return $this;
	}

	public function render(): array|string|null
	{
		switch (Application::$MODE)
		{
			case GDT::RENDER_CLI:
				return $this->renderCLI();
			case GDT::RENDER_BINARY:
				return $this->renderBinary();
			default:
				return $this->renderHTML();
		}
	}


	public function renderCLI(): string
	{
		$back = [];

		if (isset($this->gdo))
		{
			$back[] = t('id') . ': ' . $this->gdo->getID();
		}

		if ($this->hasTitle())
		{
			$back[] = $this->renderTitle();
		}
		if ($this->hasSubTitle())
		{
			$back[] = $this->renderSubTitle();
		}
		foreach ($this->getAllFields() as $gdt)
		{
// 	    	if ($label = $gdt->renderLabel())
// 	    	{
// 	    		$label .= ': ';
// 	    	}
// $back[] = $gdt->cliIcon() . $label . $gdt->renderCLI();
			$back[] = $gdt->cliIcon() . $gdt->renderCLI();
		}
		if (isset($this->footer))
		{
			$back[] = $this->footer->renderCLI();
		}
		return implode(', ', $back);
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'card_html.php', [
			'field' => $this]);
	}

	######################
	### Creation title ###
	######################

	public function renderCard(): string
	{
		return $this->renderHTML();
	}

	#####################
	### Edited Footer ###
	#####################

	/**
	 * Create a last 'edited by' footer.
	 */
	public function editorFooter(): self
	{
		/** @var GDO_User $user * */
		if ($user = $this->gdo->gdoColumnOf(GDT_EditedBy::class)->getValue())
		{
			$username = $user->renderProfileLink();
			$at = $this->gdo->gdoColumnOf(GDT_EditedAt::class)->renderHTML();
			$this->footer = GDT_Label::make()->label('edited_info', [$username, $at]);
		}
		return $this;
	}

	public function hasFooter(): bool
	{
		return isset($this->footer);
	}

}
