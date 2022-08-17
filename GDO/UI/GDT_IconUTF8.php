<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Default icon provider using UTF8 icon glyphs.
 * This is the most primitive and cheap icon rendering.
 * It is included in the core, and a reference for possible icons.
 * However, the possible icons are not limited to the ones defined here.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 * @see https://www.utf8icons.com/
 * @see \GDO\FontAwesome\FA_Icon
 */
final class GDT_IconUTF8
{
    public static array $MAP = [
        'account' => '⛁',
        'add' => '✚',
        'alert' => '!',
        'all' => '▤',
        'arrow_down' => '▼',
        'arrow_left' => '←',
        'arrow_right' => '‣',
        'arrow_up' => '▲',
        'audio' => '🎵',
        'back' => '↶',
        'bank' => '🏦',
        'bars' => '☰',
        'birthday' => '🎂',
        'block' => '✖',
        'book' => '📖',
        'bulb' => '💡',
        'calendar' => '📅',
        'captcha' => '♺',
        'caret' => '⌄',
    	'close' => '✖',
        'country' => '⚑',
        'check' => '✔',
    	'color' => '🎡',
    	'copyright' => '©',
        'create' => '✚',
        'credits' => '¢',
        'cut' => '✂',
        'delete' => '✖',
        'download' => '⇩',
        'edit' => '✎',
        'email' => '✉',
        'error' => '⚠',
    	'eye' => '👁',
        'face' => '☺',
        'female' => '♀',
        'file' => '🗎',
        'flag' => '⚑',
        'folder' => '📁',
        'font' => 'ᴫ',
        'gender' => '⚥',
        'group' => '😂',
        'guitar' => '🎸',
        'help' => '💡',
        'image' => '📷',
        'language' => '⛿',
        'level' => '🏆',
        'license' => '§',
        'like' => '❤',
        'link' => '🔗',
        'list' => '▤',
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
        'phone' => '📞',
        'plus' => '+',
    	'position' => '🗺',
        'quote' => '↶',
        'remove' => '✕',
        'reply' => '☞',
    	'required' => '❋',
        'schedule' => '☷',
        'search' => '🔍',
        'settings' => '⚙',
        'star' => '★',
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
        'wait' => '◴',
    ];
    
	public static function iconS(string $icon, string $iconText = null, string $style = null) : string
	{
	    $title = $iconText ? ' title="'.html($iconText).'"' : GDT::EMPTY_STRING;
		$_icon = isset(self::$MAP[$icon]) ? self::$MAP[$icon] : $icon;
		return "<span class=\"gdo-icon gdo-utf8-icon-$icon\"$style$title>$_icon</span>";
	}

}
