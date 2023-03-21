<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Default icon provider using UTF8 icon glyphs.
 * This is the most primitive and cheap icon rendering.
 * It is included in the core, and a reference for possible icons.
 * However, the possible icons are not limited to the ones defined here.
 *
 * @TODO: Speed up icon rendering by assigning constants.
 *
 * @version 7.0.2
 * @since 6.5.0
 * @author gizmore
 * @see https://www.utf8icons.com/
 * @see https://unicode.org/emoji/charts/full-emoji-list.html
 * @see \GDO\FontAwesome\FA_Icon
 */
final class GDT_IconUTF8
{

	public static array $MAP = [
		'account' => '⛁',
		'add' => '✚',
		'address' => '⌘',
		'alert' => '!',
		'all' => '▤',
		'amt' => '#',
		'arrow_down' => '▼',
		'arrow_left' => '←',
		'arrow_right' => '‣',
		'arrow_up' => '▲',
		'audio' => '🎵',
		'back' => '↶',
		'bank' => '🏦',
		'bars' => '☰',
		'bee' => '🐝',
		'bell' => '🔔',
		'birthday' => '🎂',
		'block' => '✖',
		'book' => '📖',
		'bulb' => '💡',
		'business' => ' 🏬',
		'calendar' => '📅',
		'captcha' => '♺',
		'caret' => '⌄',
		'cc' => '💳',
		'close' => '✖',
		'code' => '🗟',
		'construction' => '🚧',
		'country' => '⚑',
		'check' => '✔',
		'color' => '🎡',
		'copyright' => '©',
		'create' => '✚',
		'credits' => '¢',
		'cut' => '✂',
		'delete' => '✖',
		'diamond' => '❖',
		'done' => '✔',
		'done_all' => '✔',
		'download' => '⇩',
		'edit' => '✎',
		'email' => '✉',
		'error' => '⚠',
		'eye' => '👁',
		'face' => '☺',
		'female' => '♀',
		'file' => '🗎',
		'flag' => '⚑',
		'flash' => '🗲',
		'folder' => '📁',
		'font' => 'ᴫ',
		'format' => 'F',
		'gender' => '⚥',
		'group' => '😂',
		'guitar' => '🎸',
		'hands' => '🤝',
		'heart' => '♡',
		'help' => '💡',
		'house' => '🏠',
		'icecream' => '🍦',
		'image' => '📷',
		'info' => 'ⓘ',
		'language' => '⛿',
		'legal' => '⚖',
		'level' => '🏆',
		'license' => '§',
		'like' => '❤',
		'link' => '🔗',
		'list' => '▤',
		'location' => '🚩',
		'lock' => '🔒',
		'male' => '♂',
		'medal' => '🥇',
		'menu' => '≡',
		'message' => '☰',
		'minus' => '-',
		'money' => '$',
		'numeric' => 'π',
		'password' => '⚷',
		'pause' => '⏸',
		'percent' => '%',
		'phone' => '📞',
		'plus' => '+',
		'position' => '🗺',
		'print' => '🖶',
		'qrcode' => '╬',
		'question' => '?',
		'quote' => '↶',
		'remove' => '✕',
		'reply' => '☞',
		'required' => '❋',
		'schedule' => '☷',
		'search' => '🔍',
		'settings' => '⚙',
		'spiderweb' => '🕸',
		'star' => '★',
		'stop' => '❌',
		'sun' => '🌞',
		'table' => '☷',
		'tag' => '⛓',
		'thumbs_up' => '👍',
		'thumbs_down' => '👎',
		'thumbs_none' => '👉',
		'time' => '⌚',
		'title' => 'T',
		'trophy' => '🏆',
		'unicorn' => '🦄',
		'upload' => '⇧',
		'url' => '🌐',
		'user' => '☺',
		'users' => '😂',
		'view' => '👁',
		'vote' => '🗳',
		'wait' => '◴',
		'whatsapp' => '📱',
		'work' => '👷',
		'write' => '✎',
	];

	public static function iconS(string $icon, string $iconText = null, string $style = null): string
	{
		$title = $iconText ? ' title="' . html($iconText) . '"' : GDT::EMPTY_STRING;
		$_icon = isset(self::$MAP[$icon]) ? self::$MAP[$icon] : $icon;
		return "<span class=\"gdo-icon gdo-utf8-icon-$icon\"$style$title>$_icon</span>";
	}

}
