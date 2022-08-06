<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDO;
use GDO\Util\Strings;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;
use GDO\Core\GDT_Text;

/**
 * A message is a GDT_Text with an editor. Classic uses a textarea.
 * The content is html, filtered through a whitelist with html-purifier.
 * A gdo6-tinymce / ckeditor is available. Planned is markdown(done) and bbcode(planned).
 * 
 * @see \GDO\TinyMCE\Module_TinyMCE
 * @see \GDO\CKEditor\Module_CKEditor
 * @see \GDO\Markdown\Module_Markdown
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
class GDT_Message extends GDT_Text
{
    public string $icon = 'message';
    
    private ?string $msgInput = null;  # Raw user input
    private ?string $msgOutput = null; # Decoded input to output 
    private ?string $msgText = null;   # Output with removed html for search
    private ?string $msgEditor = null; # Message Codec Provider used for this message.
    
    ###########
    ### GDT ###
    ###########
    public static int $NUM = 1;
    public int $num = 0;
    /**
     * On make, setup order and search field.
     * @param string $name
     * @return self
     */
    public static function make(string $name=null) : self
    {
        $gdt = parent::make($name);
        $gdt->num = self::$NUM++;
        $gdt->orderField = $gdt->name . '_text';
        $gdt->searchField = $gdt->name . '_text';
        return $gdt;
    }
    
    ##############
    ### Quoter ###
    ##############
    public static $QUOTER = [self::class, 'QUOTE'];
    public static function QUOTE(GDO_User $user, $date, $text)
    {
        $link = GDT_ProfileLink::make()->nickname()->forUser($user);
        return sprintf("<div><blockquote>\n<span class=\"quote-by\">%s</span>\n<span class=\"quote-from\">%s</span>\n%s</blockquote>&nbsp;</div>\n",
            t('quote_by', [$link->render()]), t('quote_at', [tt($date)]), $text);
    }
    
    public static function quoteMessage(GDO_User $user, $date, $text)
    {
        return call_user_func(self::$QUOTER, $user, $date, $text);
    }
    
    ###############
    ### Decoder ###
    ###############
    public static string $EDITOR_NAME = 'HTML';
    public static array $DECODER = [self::class, 'DECODE'];
    public static array $DECODERS = [
    	'TEXT' => [self::class, 'NONDECODE'],
    	'HTML' => [self::class, 'DECODE'],
    ];
    
    public static function setDecoder($decoder)
    {
    	self::$EDITOR_NAME = $decoder;
    	self::$DECODER = self::$DECODERS[$decoder];
    }

    public static function DECODE($s)
    {
    	return self::getPurifier()->purify($s);
    }
    
    public static function NONDECODE($s)
    {
    	return html($s);
    }
    
    public static function decodeMessage($s)
    {
        if ($s === null)
        {
            return null;
        }
        return call_user_func(self::$DECODER, $s);
    }
    
    public static function plaintext($html)
    {
        if ($html === null)
        {
            return null;
        }
        $html = html_entity_decode($html, ENT_HTML5);
        $html = preg_replace("#\r?\n#", ' ', $html);
        $html = preg_replace('#<a .*href="(.*)".*>(.*)</a>#i', ' $2($1) ', $html);
        $html = preg_replace('#</p>#i', "\n", $html);
        $html = preg_replace('#<[^\\>]*>#', ' ', $html);
        $html = preg_replace('# +#', ' ', $html);
        return trim($html);
    }
    
//     public function gdoExampleVars() : ?string
//     {
//     	return t('message');
//     }
    
    ################
    ### Validate ###
    ################
    public static function getPurifier() : \HTMLPurifier
    {
        static $purifier;
        if (!isset($purifier))
        {
            require GDO_PATH . 'GDO/UI/htmlpurifier/library/HTMLPurifier.auto.php';
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('URI.Host', GDO_DOMAIN);
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('URI.DisableExternalResources', false);
            $config->set('URI.DisableResources', false);
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.Allowed', 'br,a[href|rel|target],p,pre[class],code[class],img[src|alt],figure[style|class],figcaption,center,b,i,div[class],h1,h2,h3,h4,h5,h6');
            $config->set('Attr.DefaultInvalidImageAlt', t('err_img_not_found'));
            $config->set('HTML.SafeObject', true);
            $config->set('Attr.AllowedRel', array('nofollow'));
            $config->set('HTML.DefinitionID', 'gdo6-message');
            $config->set('HTML.DefinitionRev', 1);
            if ($def = $config->maybeGetRawHTMLDefinition())
            {
                $def->addElement('figcaption', 'Block', 'Flow', 'Common');
                $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
            }
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier;
    }
    
    /**
     * Validate via String validation twice, the input and output variants.
     * {@inheritDoc}
     * @see GDT_Text::validate()
     */
    public function validate($value) : bool
    {
        # Check raw input for length and pattern etc.
        if (!parent::validate($value))
        {
            return false;
        }
        
        # Decode the message
        $decoded = self::decodeMessage($this->toVar($value));
        $text = self::plaintext($decoded);
        
        # Check decoded input for length and pattern etc.
        if (!parent::validate($decoded))
        {
            return false;
        }
        
        # Assign input variations.
        $this->msgInput = $value;
        $this->msgOutput = $decoded;
        $this->msgText = $text;
        $this->msgEditor = self::$EDITOR_NAME;
        return true;
    }
    
    ##########
    ### DB ###
    ##########
    public function gdoColumnNames() : array
    {
    	return [
    		"{$this->name}_input",
    		"{$this->name}_output",
    		"{$this->name}_text",
    		"{$this->name}_editor",
    	];
    }
    
    public function gdoColumnDefine() : string
    {
        return
        "{$this->name}_input {$this->gdoColumnDefineB()},\n".
        "{$this->name}_output {$this->gdoColumnDefineB()},\n".
        "{$this->name}_text {$this->gdoColumnDefineB()},\n".
        "{$this->name}_editor VARCHAR(16) CHARSET ascii COLLATE ascii_bin\n";
    }
    
    ######################
    ### 3 column hacks ###
    ######################
    public function initial(string $var=null) : self
    {
        $this->msgInput = $var;
        $this->msgOutput = self::decodeMessage($var);
        $this->msgText = self::plaintext($this->output);
        $this->msgEditor = $this->nowysiwyg ? 'GDT' : self::$EDITOR_NAME;
        return parent::initial($var);
    }
    
    /**
     * If we set a var, value and plaintext get's precomputed.
     * {@inheritDoc}
     * @see \GDO\Core\GDT::var()
     */
    public function var(string $var = null) : self
    {
        $this->msgInput = $var;
        $this->msgOutput = self::decodeMessage($var);
        $this->msgText = self::plaintext($this->msgOutput);
        $this->msgEditor = $this->nowysiwyg ? 'GDT' : self::$EDITOR_NAME;
        return parent::var($var);
    }
    
    public function blankData() : array
    {
        return [
            "{$this->name}_input" => $this->msgInput,
            "{$this->name}_output" => $this->msgOutput,
            "{$this->name}_text" => $this->msgText,
            "{$this->name}_editor" => self::$EDITOR_NAME,
        ];
    }
    
    public function gdo(GDO $gdo = null) : self
    {
    	return $this->var($gdo->gdoVar("{$this->name}_input"));
    }
    
    /**
     * Set GDO Data is called when the GDO sets up the GDT.
     * We copy the 3 text columns and revert a special naming hack in module news; 'iso][en][colum_name' could be it's name.
     * {@inheritDoc}
     * @see \GDO\Core\GDT::setGDOData()
     */
    public function setGDOData(array $data) : self
    {
        $name = Strings::rsubstrFrom($this->name, '[', $this->name); # @XXX: ugly hack for news tabs!
        $this->msgInput = @$data["{$name}_input"];
        $this->msgOutput = @$data["{$name}_output"];
        $this->msgText = @$data["{$name}_text"];
        $this->msgEditor = @$data["{$name}_editor"];
        return $this;
    }
    
    /**
     * getGDOData() is called when the gdo wants to update it's gdoVars.
     * This happens when formData() is plugged into saveVars() upon update and creation.
     */
    public function getGDOData() : ?array
    {
        return [
            "{$this->name}_input" => $this->msgInput,
            "{$this->name}_output" => $this->msgOutput,
            "{$this->name}_text" => $this->msgText,
            "{$this->name}_editor" => $this->msgEditor,
        ];
    }
    
    ##############
    ### Getter ###
    ##############
    public function getVar() { return $this->getVarInput(); }
    public function getVarInput() : ?string { return $this->msgInput; }
    public function getVarOutput() : ?string { return $this->msgOutput; }
    public function getVarText() : ?string { return $this->msgText; }
    
    ##############
	### Render ###
	##############
    public function renderCLI() : string { return $this->getVarText() . "\n"; }
    public function renderCell() : string { return (string) $this->getVarOutput(); }
    public function renderList() : string { return (string) $this->getVarOutput(); }
    public function renderCard() : string { return '<div class="gdt-message-card">'.$this->getVarOutput().'</div>'; }
    public function renderForm() : string { return GDT_Template::php('UI', 'form/message.php', ['field'=>$this]); }
    public function renderChoice() : string { return '<div class="gdo-message-condense">'.$this->renderCell().'</div>'; }
	
	##############
	### Editor ###
	##############
	public bool $nowysiwyg = false;
	public function nowysiwyg(bool $nowysiwyg=true) : self { $this->nowysiwyg = $nowysiwyg; return $this; }
	public function classEditor() : string { return $this->nowysiwyg ? 'as-is' : 'wysiwyg'; }
	
}
