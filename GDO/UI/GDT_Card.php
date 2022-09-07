<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_EditedAt;
use GDO\Core\GDT_EditedBy;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;
use GDO\User\WithAvatar;
use GDO\Date\GDT_DateDisplay;
use GDO\Form\WithActions;
use GDO\Core\WithFields;
use GDO\Core\Application;
use GDO\Core\WithGDO;
use GDO\User\GDT_User;

/**
 * A card with title, subtitle, creator, date, content and actions.
 *  
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.4
 */
class GDT_Card extends GDT
{
	use WithGDO;
	use WithTitle;
	use WithSubTitle;
	use WithAvatar;
	use WithFields;
	use WithActions;
	use WithPHPJQuery;
	
	###############
	### Content ###
	###############
	public GDT $content;
	public function content($content) : self
	{
		$this->content = $content;
		return $this;
	}
	
	#############
	### Image ###
	#############
	public GDT $image;
	public function image($image) : self
	{
		$this->image = $image;
		return $this;
	}
	
	##############
	### Footer ###
	##############
	public GDT $footer;
	public function footer($footer) : self
	{
		$this->footer = $footer;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function render() : string
	{
	    $app = Application::$INSTANCE;
	    if ($app->mode === GDT::RENDER_CLI)
	    {
	        return $this->renderCLI();
	    }
        return $this->renderHTML();
	}
	public function renderCard() : string { return $this->renderHTML(); }
	public function renderHTML() : string { return GDT_Template::php('UI', 'card_html.php', ['field' => $this]); }
	
	public function renderCLI() : string
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
	    	if ($label = $gdt->renderLabel())
	    	{
	    		$label .= ': ';
	    	}
	    	$back[] = $gdt->cliIcon() . $label . $gdt->renderCLI();
	    }
	    if (isset($this->footer))
	    {
	        $back[] = $this->footer->renderCLI();
	    }
	    return implode(', ', $back);
	}
	
	######################
	### Creation title ###
	######################
	/**
	 * Use the subtitle to render creation stats.
	 * User (with avatar), Date, Ago.
	 * You can override the ago subtitle.
	 * If you do not want anything, just don't use this method.
	 */
	public function creatorHeader(string $byField=null, string $atField=null, string $subtitleOverride=null, bool $subtitleNoUser=false) : self
	{
	    /** @var $user GDO_User **/
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
	    
	    $user = $byField ? $byField->getValue() : null;

	    if ($atField)
	    {
	        $atField = $this->gdo->gdoColumn($atField);
	    }
	    else
	    {
	    	$atField = $this->gdo->gdoColumnOf(GDT_CreatedAt::class);
	    }

	    # Add avatar
	    if (module_enabled('Avatar')) # ugly bridge
	    {
	    	$this->avatarUser($user);
	    }
	    
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
	        	$date = GDT_DateDisplay::make($atField->name)->gdo($this->gdo)->render();
	        }
        	$this->subtitle('creator_header', [$profileLink->render(), $date]);
        }
	    return $this;
	}
	
	#####################
	### Edited Footer ###
	#####################
	/**
	 * Create a last 'edited by' footer.
	 */
	public function editorFooter()
	{
	    /** @var $user GDO_User **/
	    if ($user = $this->gdo->gdoColumnOf(GDT_EditedBy::class)->getValue())
	    {
   	        $username = $user->renderProfileLink();
    	    $at = $this->gdo->gdoColumnOf(GDT_EditedAt::class)->renderHTML();
    	    $this->footer = GDT_Label::make()->label('edited_info', [$username, $at]);
	    }
	    return $this;
	}
	
}
