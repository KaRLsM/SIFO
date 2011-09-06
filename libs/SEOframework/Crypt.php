<?php
namespace SeoFramework;

class Crypt
{
	/**
	 * Seed used for crypt/decrypt strings. Do not change it once you started encrypting strings or passwords cannot be reverted.
	 *
	 * @var string
	 */
	static public $seed = 'WriteSomeTextHere';

	static public function encrypt( $string )
	{
		$result = '';
		for( $i=0; $i<strlen( $string ); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr( self::$seed, ($i % strlen( self::$seed ) )-1, 1 );
			$char = chr( ord( $char ) + ord( $keychar ) );
			$result .= $char;
		}

		return base64_encode( $result );
	}

	static public function decrypt( $string )
	{
		$result ='';
		$string = base64_decode( $string );

		for( $i=0; $i<strlen( $string ); $i++ ) {
			$char = substr( $string, $i, 1);
			$keychar = substr( self::$seed, ( $i % strlen( self::$seed ) )-1, 1 );
			$char = chr( ord( $char ) - ord( $keychar ) );
			$result .= $char;
		}

		return $result;
	}

	static public function encryptForUrl( $string, $char_plus = '-', $char_slash = '.' )
	{
		$string = self::encrypt( $string );
		$string = str_replace( '+', $char_plus, $string );
		$string = str_replace( '/', $char_slash, $string );
		return $string;
	}

	static public function decryptFromUrl( $string, $char_plus = '-', $char_slash = '.' )
	{
		$string = str_replace( $char_plus, '+', $string );
		$string = str_replace( $char_slash, '/', $string );
		return self::decrypt( $string );
	}
}
?>