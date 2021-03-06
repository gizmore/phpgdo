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
	use WithAvatar;
	use WithFields;
	use WithActions;
	use WithPHPJQuery;
	
	###############
	### Content ###
	###############
	/** @var $content GDT **/
	public $content;
	public function content($content) { $this->content = $content; return $this; }
	
	#############
	### Image ###
	#############
	/** @var $image GDT **/
	public $image;
	public function image($image) { $this->image = $image; return $this; }
	
	##############
	### Footer ###
	##############
	/** @var $footer GDT **/
	public $footer;
	public function footer($footer) { $this->footer = $footer; return $this; }
	
	##############
	### Render ###
	##############
	public function render() : string
	{
	    $app = Application::$INSTANCE;
	    if ($app->isCLI())
	    {
	        return $this->renderCLI();
	    }
// 	    elseif ($app->isHTML())
// 	    {
// 	        GDT_Response::$INSTANCE->addField($this);
// 	    }
// 	    else
// 	    {
	        return $this->renderCell();
// 	    }
	}
	public function renderCard() : string { return $this->renderCell(); }
	public function renderCell() : string { return GDT_Template::php('UI', 'card_html.php', ['field' => $this]); }
	
	public function renderCLI() : string
	{
	    $back = [];
	    
// 	    if ($this->gdo)
// 	    {
// 	        $back[] = t('id') . ': ' . $this->gdo->getID();
// 	    }
	    
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
	        $back[] = $gdt->renderCLI();
	    }
	    if ($this->footer)
	    {
	        $back[] = $this->footer->renderCLI();
	    }
	    return implode(', ', $back);
	}
	
	##############
	### Helper ###
	##############
	public function addLabel($key, array $args=null)
	{
	    return $this->addField(GDT_Label::make()->label($key, $args));
	}
	
	######################
	### Creation title ###
	######################
	/**
	 * Use the subtitle to render creation stats. User (with avatar), Date, Age.
	 * @return self
	 */
	public function creatorHeader($byField=null, $atField=null)
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
	    $user = $byField->getValue();

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
        $profileLink = GDT_ProfileLink::make()->user($user)->nickname();
	    $this->subtitle = GDT_Container::make()->horizontal();
        $this->subtitle->addField($profileLink);
        $this->subtitle->addField(GDT_DateDisplay::make($atField->name)->gdo($this->gdo));
	    
	    return $this;
	}
	
	public function subtitle(GDT $gdt) : self
	{
		$this->subtitle = $gdt;
		return $this;
	}
	
	public function hasSubTitle() : bool
	{
		return isset($this->subtitle) &&
			($this->subtitle->hasFields());
	}
	
	public function renderSubTitle() : string
	{
		return $this->subtitle->renderCell();
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
    	    if (module_enabled('Profile'))
    	    {
    	        $username = GDT_ProfileLink::make()->forUser($user)->nickname()->withAvatar()->renderCell();
    	    }
    	    else
    	    {
    	        $username = $user->renderUserName();
    	    }
    	    
    	    $at = $this->gdo->gdoColumnOf(GDT_EditedAt::class)->renderCell();
    	    $this->footer = GDT_Label::make()->label('edited_info', [$username, $at]);
	    }
	    return $this;
	}
	
}
