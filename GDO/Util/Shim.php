<?php
// use GDO\Core\GDO_Exception;

/**
 * Backwards compatibility. @TODO make use of the php preprocessor to make shim zero cost.
 * PHP7.4 will not work though :(
 */

if ( !function_exists('getallheaders'))
{
	/**
	 * Get all HTTP header key/values as an associative array for the current request.
	 *
	 * @return string[string] The HTTP header key/value pairs.
	 */
	function getallheaders() : array
	{
		$headers = [];

		$copy_server = [
			'CONTENT_TYPE' => 'Content-Type',
			'CONTENT_LENGTH' => 'Content-Length',
			'CONTENT_MD5' => 'Content-Md5',
		];

		foreach ($_SERVER as $key => $value)
		{
			if (str_starts_with($key, 'HTTP_'))
			{
				$key = substr($key, 5);
				if ( !isset($copy_server[$key]) || !isset($_SERVER[$key]))
				{
					$key = str_replace(' ', '-',
					ucwords(strtolower(str_replace('_', ' ', $key))));
					$headers[$key] = $value;
				}
			}
			elseif (isset($copy_server[$key]))
			{
				$headers[$copy_server[$key]] = $value;
			}
		}

		if ( !isset($headers['Authorization']))
		{
			if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
			{
				$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			}
			elseif (isset($_SERVER['PHP_AUTH_USER']))
			{
				$basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
				$headers['Authorization'] = 'Basic ' .
				base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
			}
			elseif (isset($_SERVER['PHP_AUTH_DIGEST']))
			{
				$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
			}
		}
		return $headers;
	}
}

if ( !function_exists('openssl_random_pseudo_bytes'))
{
	function openssl_random_pseudo_bytes(int $length, bool $crypto_strong=true) : string 
	{
		$rand = '';
		for ($i = 0; $i < $length; $i++)
		{
			$rand .= chr(rand(0, 255));
		}
		return $rand;
	}
	
// 	function openssl_cipher_iv_length()
// 	{
		
// 	}
	
}

if ( !function_exists('str_starts_with'))
{
	function str_starts_with(string $haystack, string $needle) : bool
	{
		return strpos($haystack, $needle) === 0;
	}
}

if ( !function_exists('str_ends_with'))
{
	function str_ends_with(string $haystack, string $needle) : bool
	{
		return substr_compare($haystack, $needle, -strlen($needle)) === 0;
	}
}

/**
 * Dangling breakpoints throw an exception.
 */
// if ( !function_exists('xdebug_break'))
// {
// 	function xdebug_break() : void
// 	{
// // 		throw new GDO_Exception('A breakpoint has been encountered. OOPS!');
// 	}
// }
