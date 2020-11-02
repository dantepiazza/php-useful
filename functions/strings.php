<?php

	function strupperlen($string, $porcentaje = 0.3){
		$longitud = strlen($string);
			
		$mayusculas = preg_match_all('/([A-Z]{1})/', $string);
			
		if($mayusculas > ceil($longitud * $porcentaje)){
			return true;
		}
			
		return false;
	}	
	
	function get_permastring($Dato, $Remplazar = array(), $Delimitador = '-'){
		if(is_null($Remplazar) or !empty($Remplazar)){
			$Dato = str_replace((array)$Remplazar, ' ', $Dato);
		}

		$Cadena = remove_accents($Dato);
		$Cadena = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $Cadena);
		$Cadena = strtolower(trim($Cadena, '-'));
		$Cadena = preg_replace("/[\/_|+ -]+/", $Delimitador, $Cadena);

		return $Cadena;
	}

	function generate_key($Longitud, $Especiales = false){
		$Clave = '';
		$Base = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPKRSTUVWXYZ";
		
		if($Especiales){
			$Base .= '!@#$%^&*()`~-_=+[{]}\\|;\':",<.>/?';
		}	
		
		for($C = 0; $C < $Longitud; $C++) {
			$Clave .= $Base{rand(0, strlen($Base))};
		}
		
		return $Clave;
	}
	
	function is_version($version){
		return preg_match("/^(\d+\.)?(\d+\.)?(\*|\d+)$/", $version);
	}
	
	function is_serialized($data){
		// if it isn't a string, it isn't serialized
		if ( ! is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		$length = strlen( $data );
		if ( $length < 4 )
			return false;
		if ( ':' !== $data[1] )
			return false;
		$lastc = $data[$length-1];
		if ( ';' !== $lastc && '}' !== $lastc )
			return false;
		$token = $data[0];
		
		switch( $token ){
			case 's' :
				if ( '"' !== $data[$length-2] )
					return false;
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;\$/", $data );
		}
		
		return false;
	}
	
	function is_email($email, $deprecated = false ) {
		if( strlen( $email ) < 3 ){
			return false;
		}

		if( strpos( $email, '@', 1 ) === false ){
			return false;
		}

		list( $local, $domain ) = explode( '@', $email, 2 );

		if( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ){
			return false;
		}

		if( preg_match( '/\.{2,}/', $domain ) ){
			return false;
		}

		if( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ){
			return false;
		}

		$subs = explode( '.', $domain );

		if( 2 > count( $subs ) ){
			return false;
		}

		foreach ( $subs as $sub ) {
			if( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ){
				return false;
			}

			if( !preg_match('/^[a-z0-9-]+$/i', $sub ) ){
				return false;
			}
		}

		return true;
	}
	
	function remove_accents( $string, $locale = null) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;
	
		$seems_utf8 = function($str){
			$mbstring_binary_safe_encoding = function( $reset = false ) {
				static $encodings = array();
				static $overloaded = null;
			 
				if ( is_null( $overloaded ) )
					$overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_overload' ) & 2 );
			 
				if ( false === $overloaded )
					return;
			 
				if ( ! $reset ) {
					$encoding = mb_internal_encoding();
					array_push( $encodings, $encoding );
					mb_internal_encoding( 'ISO-8859-1' );
				}
			 
				if ( $reset && $encodings ) {
					$encoding = array_pop( $encodings );
					mb_internal_encoding( $encoding );
				}
			};
			
			$mbstring_binary_safe_encoding();
			$length = strlen($str);
			$mbstring_binary_safe_encoding(true);
	        
			for ($i=0; $i < $length; $i++) {
	                $c = ord($str[$i]);
	                if ($c < 0x80) $n = 0; // 0bbbbbbb
	                elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
	                elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
	                elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
	                elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
	                elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
	                else return false; // Does not match any model
	                for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
	                        if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
	                                return false;
	                }
	        }
	        return true;
		};
		
		
		if($seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			'�' => 'a', '�' => 'o',
			'�' => 'A', '�' => 'A',
			'�' => 'A', '�' => 'A',
			'�' => 'A', '�' => 'A',
			'�' => 'AE','�' => 'C',
			'�' => 'E', '�' => 'E',
			'�' => 'E', '�' => 'E',
			'�' => 'I', '�' => 'I',
			'�' => 'I', '�' => 'I',
			'�' => 'D', '�' => 'N',
			'�' => 'O', '�' => 'O',
			'�' => 'O', '�' => 'O',
			'�' => 'O', '�' => 'U',
			'�' => 'U', '�' => 'U',
			'�' => 'U', '�' => 'Y',
			'�' => 'TH','�' => 's',
			'�' => 'a', '�' => 'a',
			'�' => 'a', '�' => 'a',
			'�' => 'a', '�' => 'a',
			'�' => 'ae','�' => 'c',
			'�' => 'e', '�' => 'e',
			'�' => 'e', '�' => 'e',
			'�' => 'i', '�' => 'i',
			'�' => 'i', '�' => 'i',
			'�' => 'd', '�' => 'n',
			'�' => 'o', '�' => 'o',
			'�' => 'o', '�' => 'o',
			'�' => 'o', '�' => 'o',
			'�' => 'u', '�' => 'u',
			'�' => 'u', '�' => 'u',
			'�' => 'y', '�' => 'th',
			'�' => 'y', '�' => 'O',
			// Decompositions for Latin Extended-A
			'A' => 'A', 'a' => 'a',
			'A' => 'A', 'a' => 'a',
			'A' => 'A', 'a' => 'a',
			'C' => 'C', 'c' => 'c',
			'C' => 'C', 'c' => 'c',
			'C' => 'C', 'c' => 'c',
			'C' => 'C', 'c' => 'c',
			'D' => 'D', 'd' => 'd',
			'�' => 'D', 'd' => 'd',
			'E' => 'E', 'e' => 'e',
			'E' => 'E', 'e' => 'e',
			'E' => 'E', 'e' => 'e',
			'E' => 'E', 'e' => 'e',
			'E' => 'E', 'e' => 'e',
			'G' => 'G', 'g' => 'g',
			'G' => 'G', 'g' => 'g',
			'G' => 'G', 'g' => 'g',
			'G' => 'G', 'g' => 'g',
			'H' => 'H', 'h' => 'h',
			'H' => 'H', 'h' => 'h',
			'I' => 'I', 'i' => 'i',
			'I' => 'I', 'i' => 'i',
			'I' => 'I', 'i' => 'i',
			'I' => 'I', 'i' => 'i',
			'I' => 'I', 'i' => 'i',
			'?' => 'IJ','?' => 'ij',
			'J' => 'J', 'j' => 'j',
			'K' => 'K', 'k' => 'k',
			'?' => 'k', 'L' => 'L',
			'l' => 'l', 'L' => 'L',
			'l' => 'l', 'L' => 'L',
			'l' => 'l', '?' => 'L',
			'?' => 'l', 'L' => 'L',
			'l' => 'l', 'N' => 'N',
			'n' => 'n', 'N' => 'N',
			'n' => 'n', 'N' => 'N',
			'n' => 'n', '?' => 'n',
			'?' => 'N', '?' => 'n',
			'O' => 'O', 'o' => 'o',
			'O' => 'O', 'o' => 'o',
			'O' => 'O', 'o' => 'o',
			'�' => 'OE','�' => 'oe',
			'R' => 'R','r' => 'r',
			'R' => 'R','r' => 'r',
			'R' => 'R','r' => 'r',
			'S' => 'S','s' => 's',
			'S' => 'S','s' => 's',
			'S' => 'S','s' => 's',
			'�' => 'S', '�' => 's',
			'T' => 'T', 't' => 't',
			'T' => 'T', 't' => 't',
			'T' => 'T', 't' => 't',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			'W' => 'W', 'w' => 'w',
			'Y' => 'Y', 'y' => 'y',
			'�' => 'Y', 'Z' => 'Z',
			'z' => 'z', 'Z' => 'Z',
			'z' => 'z', '�' => 'Z',
			'�' => 'z', '?' => 's',
			// Decompositions for Latin Extended-B
			'?' => 'S', '?' => 's',
			'?' => 'T', '?' => 't',
			// Euro Sign
			'�' => 'E',
			// GBP (Pound) Sign
			'�' => '',
			// Vowels with diacritic (Vietnamese)
			// unmarked
			'O' => 'O', 'o' => 'o',
			'U' => 'U', 'u' => 'u',
			// grave accent
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'E', '?' => 'e',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'U', '?' => 'u',
			'?' => 'Y', '?' => 'y',
			// hook
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'E', '?' => 'e',
			'?' => 'E', '?' => 'e',
			'?' => 'I', '?' => 'i',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'U', '?' => 'u',
			'?' => 'U', '?' => 'u',
			'?' => 'Y', '?' => 'y',
			// tilde
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'E', '?' => 'e',
			'?' => 'E', '?' => 'e',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'U', '?' => 'u',
			'?' => 'Y', '?' => 'y',
			// acute accent
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'E', '?' => 'e',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'U', '?' => 'u',
			// dot below
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'A', '?' => 'a',
			'?' => 'E', '?' => 'e',
			'?' => 'E', '?' => 'e',
			'?' => 'I', '?' => 'i',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'O', '?' => 'o',
			'?' => 'U', '?' => 'u',
			'?' => 'U', '?' => 'u',
			'?' => 'Y', '?' => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin)
			'?' => 'a',
			// macron
			'U' => 'U', 'u' => 'u',
			// acute accent
			'U' => 'U', 'u' => 'u',
			// caron
			'A' => 'A', 'a' => 'a',
			'I' => 'I', 'i' => 'i',
			'O' => 'O', 'o' => 'o',
			'U' => 'U', 'u' => 'u',
			'U' => 'U', 'u' => 'u',
			// grave accent
			'U' => 'U', 'u' => 'u',
			);
	 
			if ( 'de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale ) {
				$chars[ '�' ] = 'Ae';
				$chars[ '�' ] = 'ae';
				$chars[ '�' ] = 'Oe';
				$chars[ '�' ] = 'oe';
				$chars[ '�' ] = 'Ue';
				$chars[ '�' ] = 'ue';
				$chars[ '�' ] = 'ss';
			} elseif ( 'da_DK' === $locale ) {
				$chars[ '�' ] = 'Ae';
				$chars[ '�' ] = 'ae';
				$chars[ '�' ] = 'Oe';
				$chars[ '�' ] = 'oe';
				$chars[ '�' ] = 'Aa';
				$chars[ '�' ] = 'aa';
			} elseif ( 'ca' === $locale ) {
				$chars[ 'l�l' ] = 'll';
			} elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
				$chars[ '�' ] = 'DJ';
				$chars[ 'd' ] = 'dj';
			}
	 
			$string = strtr($string, $chars);
		} else {
			$chars = array();
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
				."\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
				."\xc3\xc4\xc5\xc7\xc8\xc9\xca"
				."\xcb\xcc\xcd\xce\xcf\xd1\xd2"
				."\xd3\xd4\xd5\xd6\xd8\xd9\xda"
				."\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
				."\xe4\xe5\xe7\xe8\xe9\xea\xeb"
				."\xec\xed\xee\xef\xf1\xf2\xf3"
				."\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
				."\xfc\xfd\xff";
	 
			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
	 
			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars = array();
			$double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
	 
		return $string;
	}

?>