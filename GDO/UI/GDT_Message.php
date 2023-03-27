<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDO;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Text;
use GDO\DB\Query;
use GDO\DBMS\Module_DBMS;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;

/**
 * A message is a GDT_Text with an editor.
 *
 * It will add 4 DataBase columns when added to a GDO: _input, _output, _text and _editor.
 * This way messages can be searched nicely, rendered quickly and beeing edited correctly.
 *
 * Classic uses a textarea when no editor module is installed.
 * I.e. the default Text renderer is used, which is just htmlspecialchars($input).
 *
 * The saved pre-rendered content is just final HTML,
 * filtered through a whitelist with html-purifier, when using other editor modules.
 * There are various editor modules available.
 *
 * An editor has to provide a renderer and a quotemsg generator (quoty by foo at 13:37)
 * An HTML to editor "back-encoder" is not required or used / supported / needed.
 *
 * @version 7.0.3
 * @since 4.0.0
 *
 * @see \GDO\CKEditor\Module_CKEditor
 * @see \GDO\Markdown\Module_Markdown
 * @see \GDO\SimpleMDE\Module_SimpleMDE
 * @see \GDO\HTML\Module_HTML
 *
 * @author gizmore
 */
class GDT_Message extends GDT_Text
{

	/**
	 * @deprecated
	 */
	public static int $NUM = 1;

	public static array $QUOTER = [
		self::class,
		'QUOTE',
	];

	# Decoded input to output
	/**
	 * Current editor name.
	 * Default is raw HTML with html purifier filter.
	 */
	public static string $EDITOR_NAME = 'Text';

	# Output with removed html for search
	public static array $EDITORS = [
		'Text' => [self::class, 'ESCAPE'],
	];

	public static array $DECODER = [
		self::class,
		'ESCAPE',
	];

	/**
	 * Available editors.
	 * @var callable[]
	 */
	public static array $DECODERS = [
		'Text' => [
			self::class,
			'ESCAPE',
		],
	];

	public string $icon = 'message';

	/**
	 * @deprecated
	 */
	public int $num = 0;

	# #############
	# ## Quoter ###
	# #############

	public int $textRows = 5;
	/**
	 * Do not attach the editor to the textarea.
	 * Use a simple textarea.
	 */
	public bool $nowysiwyg = false;

	private ?string $msgInput = null;

	# ##############
	# ## Decoder ###
	# ##############
	private ?string $msgOutput = null;
	private ?string $msgText = null;
	private ?string $msgEditor = null;

	public static function quoteMessage(GDO_User $user, string $date, string $text): string
	{
		return call_user_func(self::$QUOTER, $user, $date, $text);
	}

	public static function QUOTE(GDO_User $user, string $date, string $text): string
	{
		$link = GDT_ProfileLink::make()->nickname()->avatar()->user($user);
		return sprintf(
			"<div><blockquote>\n<span class=\"quote-by\">%s</span>\n<span class=\"quote-from\">%s</span>\n<br/>%s</blockquote>&nbsp;</div>\n",
			t('quote_by', [
				$link->render(),
			]), t('quote_at', [
			tt($date),
		]), $text);
	}

	/**
	 * On make, setup order and search field.
	 */
	public static function make(string $name = null): static
	{
		$gdt = parent::make($name);
		$gdt->num = self::$NUM++;
// 		$gdt->orderField = $gdt->name . '_text';
// 		$gdt->searchField = $gdt->name . '_text';
		return $gdt;
	}

	/**
	 * Validate via String validation twice, the input and output variants.
	 *
	 * @see GDT_Text::validate()
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
// 		# Check raw input for length and pattern etc.
// 		if ( !parent::validate($value))
// 		{
// 			return false;
// 		}

		# Decode the message
		$decoded = self::decodeText($this->toVar($value));
		$text = self::plaintext($decoded);

		# Check decoded input for length and pattern etc.
		if (!parent::validate($decoded))
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

	public static function decodeText(?string $s): ?string
	{
		$decoded = '';
		if ($s !== null)
		{
			$decoded = call_user_func(self::$DECODER, $s);
			$decoded = trim($decoded);
		}
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
			$html = preg_replace('#<a .*href="(.*)".*>(.*)</a>#i', ' $2 ( $1 ) ', $html);
			$html = preg_replace('#</p>#i', "\n", $html);
			$html = preg_replace('#</div>#i', "\n", $html);
			$html = preg_replace('#<[^>]*>#', ' ', $html);
			$html = preg_replace('#\s+#', ' ', $html);
			$html = trim($html);
			return $html === '' ? null : $html;
		}
		return null;
	}

	###############
	### Editors ###
	###############

	public function gdoExampleVars(): ?string
	{
		return t('message');
	}

	# ###############
	# ## Validate ###
	# ###############

	public function gdoColumnNames(): array
	{
		return [
			"{$this->name}_input",
			"{$this->name}_output",
			"{$this->name}_text",
			"{$this->name}_editor",
		];
	}

	/**
	 * Re-Using GDT_Text and GDT_String.
	 */
	public function gdoColumnDefine(): string
	{
		$dbms = Module_DBMS::instance();
		return "{$this->name}_input {$dbms->Core_GDT_TextB($this)},\n" .
			"{$this->name}_output {$dbms->Core_GDT_TextB($this)},\n" .
			"{$this->name}_text {$dbms->Core_GDT_TextB($this)},\n" .
			GDT_String::make("{$this->name}_editor")->max(16)->ascii()->caseS()->gdoColumnDefine();
	}

	# #########
	# ## DB ###
	# #########

	public function initial(?string $initial): static
	{
		$this->msgInput = $initial;
		$this->msgOutput = self::decodeText($initial);
		$this->msgText = self::plaintext($this->msgOutput);
		$this->msgEditor = $this->nowysiwyg ? 'Text' : self::$EDITOR_NAME;
		return parent::initial($initial);
	}

	/**
	 * Add a decoder.
	 * As modules populate this by priortity, the most enhanced one wins the default race.
	 * For plaintext textareas, "Text" - the default editor - is used again.
	 */
	public static function addDecoder(string $decoder, array $callable): void
	{
		self::$EDITOR_NAME = $decoder;
		self::$DECODERS[$decoder] = self::$DECODER = $callable;
	}

	# #####################
	# ## 4 columns hack ###
	# #####################

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
	public static function decodeMessage(GDT_Message $message): ?string
	{
		return self::decodeText($message->getVarInput());
	}

	public function getVarInput(): ?string
	{
		return $this->msgInput;
	}

	public static function ESCAPE(?string $s): ?string
	{
		return $s !== null ? html($s) : null;
	}

	/**
	 * If we set a var, value and plaintext get's precomputed.
	 */
	public function var(?string $var): static
	{
		$this->var = $var;
 		$this->valueConverted = false;
		$this->msgInput = $var;
		$this->msgOutput = self::decodeText($var);
		$this->msgText = self::plaintext($this->msgOutput);
// 		$this->msgEditor = $this->nowysiwyg ? 'HTML' : self::$EDITOR_NAME;
		return $this;
	}

	public function textRows(int $rows): self
	{
		$this->textRows = $rows;
		return $this;
	}

	public function htmlRows(): string
	{
		return " rows=\"{$this->textRows}\"";
	}

	public function nowysiwyg(bool $nowysiwyg = true): self
	{
		$this->nowysiwyg = $nowysiwyg;
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

	public function classEditor(): string
	{
		return $this->nowysiwyg ? 'as-is' : ('wysiwyg gdt-editor-' . $this->getWantedEditorName());
	}

	protected function getWantedEditorName(): string
	{
		return strtolower(self::$EDITOR_NAME);
	}

	public function getVar(): string|array|null
	{
		$name = $this->getName();
		if (isset($this->inputs[$name]))
		{
			$input = $this->inputs[$name];
		}
		elseif (isset($this->inputs["{$name}_input"]))
		{
			$input = $this->inputs["{$name}_input"];
		}
		else
		{
			return $this->msgInput;
		}
		return $this->inputToVar($input);
	}



	public function gdo(?GDO $gdo): static
	{
		return $this->var($gdo->gdoVar("{$this->name}_input"));
	}


	public function inputToVar(array|string|null|GDT_Method $input): ?string
	{
		return parent::inputToVar(trim((string)$input));
	}


	/**
	 * Set GDO Data is called when the GDO sets up the GDT.
	 * We copy the 3 text columns and revert a special naming hack in module news; 'iso][en][colum_name' could be it's name.
	 */
	public function setGDOData(array $data): static
	{
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
	public function getGDOData(): array
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
		return (string) $this->getVarText();
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
		return
			'<div class="gdt-card-label">' . $this->htmlIcon() . $this->renderLabelText() . "</div>\n" .
			'<div class="gdt-card-message">' . $this->getVarOutput() . "</div>\n";
	}

	public function renderForm(): string
	{
		return GDT_Template::php('UI', 'message_form.php', [
			'field' => $this,
		]);
	}

	public function renderOption(): string
	{
		return '<div class="gdo-message-condense">' . $this->renderHTML() . '</div>';
	}

	##############
	### Search ###
	##############
	public function searchQuery(Query $query, string $searchTerm): static
	{
		if ($this->isSearchable())
		{
			$search = GDO::escapeSearchS($searchTerm);
			$query->orWhere("{$this->name}_text LIKE '%{$search}%'");
		}
		return $this;
	}

}
