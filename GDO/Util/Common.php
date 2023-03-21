<?php
namespace GDO\Util;

/**
 * Often used stuff.
 *
 * @version 7.0.2
 * @since 1.0.0
 * @deprecated
 * @author gizmore
 */
final class Common
{

	##################
	### Get / Post ###
	##################
	public static function getGet($var, $default = null) { return isset($_GET[$var]) ? $_GET[$var] : $default; }

	public static function getGetInt($var, $default = 0) { return isset($_GET[$var]) && is_string($_GET[$var]) ? (int)$_GET[$var] : $default; }

	public static function getGetFloat($var, $default = 0) { return isset($_GET[$var]) && is_string($_GET[$var]) ? (float)$_GET[$var] : $default; }

	public static function getGetString($var, $default = null) { return isset($_GET[$var]) && is_string($_GET[$var]) ? $_GET[$var] : $default; }

	public static function getGetArray($var, $default = []) { return (isset($_GET[$var]) && is_array($_GET[$var])) ? $_GET[$var] : $default; }

	public static function getPost($var, $default = null) { return isset($_POST[$var]) ? ($_POST[$var]) : $default; }

	public static function getPostInt($var, $default = 0) { return isset($_POST[$var]) ? (int)$_POST[$var] : $default; }

	public static function getPostFloat($var, $default = 0) { return isset($_POST[$var]) ? (float)$_POST[$var] : $default; }

	public static function getPostString($var, $default = null) { return isset($_POST[$var]) ? (string)$_POST[$var] : $default; }

	public static function getPostArray($var, $default = []) { return (isset($_POST[$var]) && is_array($_POST[$var])) ? $_POST[$var] : $default; }

	public static function getRequest($var, $default = null) { return isset($_REQUEST[$var]) ? ($_REQUEST[$var]) : $default; }

	public static function getRequestInt($var, $default = 0) { return isset($_REQUEST[$var]) ? (int)$_REQUEST[$var] : $default; }

	public static function getRequestFloat($var, $default = 0.0) { return isset($_REQUEST[$var]) ? (float)$_REQUEST[$var] : $default; }

	public static function getRequestString($var, $default = null) { return isset($_REQUEST[$var]) ? (string)$_REQUEST[$var] : $default; }

	public static function getRequestArray($var, $default = []) { return (isset($_REQUEST[$var]) && is_array($_REQUEST[$var])) ? $_REQUEST[$var] : $default; }

}
