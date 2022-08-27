<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;
use GDO\Core\GDT_Text;
use GDO\Util\Strings;

/**
 * A message is a GDT_Text with an editor.
 * 
 * It will add 4 DB columns when added to a GDO: _input, _output, _text and _editor.
 * This way messages can be searched nicely, rendered quickly and beeing edited nicely.
 * 
 * Classic uses a textarea when no editor module is installed.
 * I.e. the default HTML as-is renderer is used.
 * There is also a TEXT renderer which is just htmlspecialchars($input).
 * 
 * The saved pre-rendered content is just final HTML,
 * filtered through a whitelist with html-purifier.
 * There are various editor modules available.
 * 
 * An editor has to provide a renderer and a quotemsg generator (quoty by foo at 13:37)
 * An HTML to editor back-encoder is not required or used / supported.
 * 
 * @TODO: Allow users to choose an editor of the installed ones. Currently only 1 editor can be installed.
 *
 * @see \GDO\TinyMCE\Module_TinyMCE
 * @see \GDO\BBCode\Module_BBCode
 * @see \GDO\CKEditor\Module_CKEditor
 * @see \GDO\Markdown\Module_Markdown
 * @see \GDO\SimpleMDE\Module_SimpleMDE
 *
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
class GDT_Message extends GDT_Text
{
	public string $icon = 'message';

	# Raw user input
	private ?string $msgInput = null;

	# Decoded input to output
	private ?string $msgOutput = null;

	# Output with removed html for search
	private ?string $msgText = null;

	# Message Codec Provider used to edit this message.
	private ?string $msgEditor = null;

	# ##########
	# ## GDT ###
	# ##########
	/**
	 * @deprecated
	 */
	public static int $NUM = 1;

	/**
	 * @deprecated
	 */
	public int $num = 0;

	/**
	 * On make, setup order and search field.
	 */
	public static function make(string $name = null): self
	{
		$gdt = parent::make($name);
		$gdt->num = self::$NUM++;
// 		$gdt->orderField = $gdt->name . '_text';
// 		$gdt->searchField = $gdt->name . '_text';
		return $gdt;
	}

	# #############
	# ## Quoter ###
	# #############
	/**
	 * @var callable The quotemsg generator.
	 */
	public static $QUOTER = [
		self::class,
		'QUOTE'
	];

	public static function quoteMessage(GDO_User $user, string $date, string $text): string
	{
		return call_user_func(self::$QUOTER, $user, $date, $text);
	}

	public static function QUOTE(GDO_User $user, string $date, string $text): string
	{
		$link = GDT_ProfileLink::make()->nickname()->user($user);
		return sprintf(
			"<div><blockquote>\n<span class=\"quote-by\">%s</span>\n<span class=\"quote-from\">%s</span>\n<br/>%s</blockquote>&nbsp;</div>\n",
			t('quote_by', [
				$link->render()
			]), t('quote_at', [
				tt($date)
			]), $text);
	}

	# ##############
	# ## Decoder ###
	# ##############
	/**
	 * Current editor name.
	 * Default is raw HTML with html purifier filter.
	 */
	public static string $EDITOR_NAME = 'HTML';

	/**
	 * @var callable current editor's decoder method.
	 */
	public static array $DECODER = [
		self::class,
		'DECODE'
	];

	/**
	 * Available editors.
	 * @var callable[string]
	 */
	public static array $DECODERS = [
		'TEXT' => [
			self::class,
			'ESCAPE'
		],
		'HTML' => [
			self::class,
			'DECODE'
		],
	];

	/**
	 * Set the current editor.
	 */
	public static function setDecoder(string $decoder): void
	{
		self::$EDITOR_NAME = $decoder;
		self::$DECODER = self::$DECODERS[$decoder];
	}

	/**
	 * Decode a message with the current editor.
	 */
	public static function decodeMessage(?string $s): ?string
	{
		if ($s === null)
		{
			return null;
		}
		$decoded = call_user_func(self::$DECODER, $s);
		$decoded = trim($decoded);
		return $decoded === '' ? null : $decoded;
	}
	
	/**
	 * Strip HTML from all it's tags to generate a searchable plaintext.
	 * Convert anchors to a plaintext URL, like foo(bla.com).
	 */
	public static function plaintext(?string $html): ?string
	{
		if ($html !== null)
		{
			$html = html_entity_decode($html, ENT_HTML5);
			$html = preg_replace("#\r?\n#", ' ', $html);
			$html = preg_replace('#<a .*href="(.*)".*>(.*)</a>#i', ' $2($1) ', $html);
			$html = preg_replace('#</p>#i', "\n", $html);
			$html = preg_replace('#<[^\\>]*>#', ' ', $html);
			$html = preg_replace('# +#', ' ', $html);
			$html = trim($html);
			return $html === '' ? null : $html;
		}
		return null;
	}
	
	###############
	### Editors ###
	###############
	public static function DECODE(?string $s): ?string
	{
		return self::getPurifier()->purify(Strings::nl2brHTMLSafe($s));
	}

	public static function ESCAPE(?string $s): ?string
	{
		return $s !== null ? html($s) : null;
	}

	# ###############
	# ## Validate ###
	# ###############
	public static function getPurifier(): \HTMLPurifier
	{
		static $purifier;
		if ( !isset($purifier))
		{
			require GDO_PATH . 'GDO/UI/htmlpurifier/library/HTMLPurifier.auto.php';
			$config = \HTMLPurifier_Config::createDefault();
			$config->set('URI.Host', GDO_DOMAIN);
			$config->set('HTML.Nofollow', true);
			$config->set('HTML.Doctype', 'HTML 4.01 Transitional'); # HTML5 not working
			$config->set('URI.DisableExternalResources', false);
			$config->set('URI.DisableResources', false);
			$config->set('HTML.TargetBlank', true);
			$config->set('HTML.Allowed',
				'br,a[href|rel|target],p,pre[class],code[class],img[src|alt],figure[style|class],figcaption,center,b,i,div[class],h1,h2,h3,h4,h5,h6,blockquote,span,em,i,b');
			$config->set('Attr.DefaultInvalidImageAlt', t('err_img_not_found'));
			$config->set('HTML.SafeObject', true);
			$config->set('Attr.AllowedRel', [
				'nofollow'
			]);
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
	 *
	 * @see GDT_Text::validate()
	 */
	public function validate($value): bool
	{
		# Check raw input for length and pattern etc.
		if ( !parent::validate($value))
		{
			return false;
		}

		# Decode the message
		$decoded = self::decodeMessage($this->toVar($value));
		$text = self::plaintext($decoded);

		# Check decoded input for length and pattern etc.
		if ( !parent::validate($decoded))
		{
			return false;
		}

		# Assign input variations.
// 		$this->var = $value;
		$this->msgInput = $value;
		$this->msgOutput = $decoded;
		$this->msgText = $text;
		$this->msgEditor = self::$EDITOR_NAME;
		return true;
	}

	public function gdoExampleVars(): ?string
	{
		return t('message');
	}
	
	# #########
	# ## DB ###
	# #########
	public function gdoColumnNames(): array
	{
		return [
			"{$this->name}_input",
			"{$this->name}_output",
			"{$this->name}_text",
			"{$this->name}_editor",
		];
	}

	public function gdoColumnDefine(): string
	{
		return "{$this->name}_input {$this->gdoColumnDefineB()},\n" .
			"{$this->name}_output {$this->gdoColumnDefineB()},\n" .
			"{$this->name}_text {$this->gdoColumnDefineB()},\n" .
			"{$this->name}_editor VARCHAR(16) CHARSET ascii COLLATE ascii_bin\n";
	}

	# #####################
	# ## 4 columns hack ###
	# #####################
	public function initial(string $var = null): self
	{
		$this->msgInput = $var;
		$this->msgOutput = self::decodeMessage($var);
		$this->msgText = self::plaintext($this->msgOutput);
		$this->msgEditor = $this->nowysiwyg ? 'HTML' : self::$EDITOR_NAME;
		return parent::initial($var);
	}

	/**
	 * If we set a var, value and plaintext get's precomputed.
	 */
	public function var(string $var = null): self
	{
		$this->var = $var;
// 		$this->valueConverted = false;
		$this->msgInput = $var;
		$this->msgOutput = self::decodeMessage($var);
		$this->msgText = self::plaintext($this->msgOutput);
		$this->msgEditor = $this->nowysiwyg ? 'HTML' : self::$EDITOR_NAME;
		return $this;
	}

	public function blankData(): array
	{
		return [
			"{$this->name}_input" => $this->msgInput,
			"{$this->name}_output" => $this->msgOutput,
			"{$this->name}_text" => $this->msgText,
			"{$this->name}_editor" => self::$EDITOR_NAME,
		];
	}

	public function getVar()
	{
		$name = $this->getName();
		if (isset($this->inputs[$name]))
		{
			$input = $this->inputs[$name];
			return $this->inputToVar($input);
		}
		if (isset($this->inputs["{$name}_input"]))
		{
			$input = $this->inputs["{$name}_input"];
			return $this->inputToVar($input);
		}
		return $this->msgInput;
	}

	public function gdo(GDO $gdo = null): self
	{
		return $this->var($gdo->gdoVar("{$this->name}_input"));
	}

	/**
	 * Set GDO Data is called when the GDO sets up the GDT.
	 * We copy the 3 text columns and revert a special naming hack in module news; 'iso][en][colum_name' could be it's name.
	 */
	public function setGDOData(array $data): self
	{
// 		$name = Strings::rsubstrFrom($this->name, '[', $this->name); # @XXX: ugly hack for news tabs!
		$name = $this->name;
		if (isset($data[$name]))
		{
			$this->var($data[$name]);
		}
		else
		{
			$this->msgInput = @$data["{$name}_input"];
			$this->msgOutput = @$data["{$name}_output"];
			$this->msgText = @$data["{$name}_text"];
			$this->msgEditor = @$data["{$name}_editor"];
			$this->var = $this->msgInput;
		}
		return $this;
	}

	/**
	 * getGDOData() is called when the gdo wants to update it's gdoVars.
	 * This happens when formData() is plugged into saveVars() upon update and creation.
	 */
	public function getGDOData(): ?array
	{
		return [
			"{$this->name}_input" => $this->msgInput,
			"{$this->name}_output" => $this->msgOutput,
			"{$this->name}_text" => $this->msgText,
			"{$this->name}_editor" => $this->msgEditor,
		];
	}

	# #############
	# ## Getter ###
	# #############
	public function getVarInput(): ?string
	{
		return $this->msgInput;
	}

	public function getVarOutput(): ?string
	{
		return $this->msgOutput;
	}

	public function getVarText(): ?string
	{
		return $this->msgText;
	}

	# #############
	# ## Render ###
	# #############
	public function renderCLI(): string
	{
		return $this->getVarText() . "\n";
	}

	public function renderHTML(): string
	{
		return (string) $this->getVarOutput();
	}

	public function renderList(): string
	{
		return (string) $this->getVarOutput();
	}

	public function renderCard(): string
	{
		$label = '<div class="gdt-card-label">' . $this->htmlIcon() . $this->renderLabelText() . "</div>\n";
		$messg = '<div class="gdt-card-message">' . $this->getVarOutput() . "</div>\n";
		return $label . $messg;
	}

	public function renderForm(): string
	{
		return GDT_Template::php('UI', 'message_form.php', [
			'field' => $this
		]);
	}

	public function renderOption(): string
	{
		return '<div class="gdo-message-condense">' . $this->renderHTML() . '</div>';
	}
	
	############
	### Rows ###
	############
	public int $textRows = 5;
	public function textRows(int $rows) : self
	{
		$this->textRows = $rows;
		return $this;
	}
	
	public function htmlRows() : string
	{
		return " rows=\"{$this->textRows}\"";
	}

	# #############
	# ## Editor ###
	# #############
	/**
	 * Do not attach the editor to the textarea.
	 * Use a simple textarea.
	 */
	public bool $nowysiwyg = false;

	public function nowysiwyg(bool $nowysiwyg = true): self
	{
		$this->nowysiwyg = $nowysiwyg;
		return $this;
	}

	public function classEditor(): string
	{
		return $this->nowysiwyg ? 'as-is' : ('wysiwyg gdt-editor-' . $this->getWantedEditorName());
	}

	protected function getWantedEditorName() : string
	{
		return strtolower(self::$EDITOR_NAME);
	}
	
}
