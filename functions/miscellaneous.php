<?php
	
	function CryptoJSAesDecrypt($passphrase, $jsonString){
		$jsondata = $jsonString;
		
		try {
			$salt = hex2bin($jsondata["salt"]);
			$iv  = hex2bin($jsondata["iv"]);          
		} catch(Exception $e) { return null; }

		$ciphertext = base64_decode($jsondata["ciphertext"]);
		$iterations = 999; //same as js encrypting 

		$key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

		$decrypted = openssl_decrypt($ciphertext , 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

		return $decrypted;
	}
	
	function paginate($Cantidad_Registros, $Cantidad_Paginar){
		if($Cantidad_Registros > $Cantidad_Paginar){
			$URI = substr(strstr($_SERVER['REQUEST_URI'],'?'), 1);
			$Paginado = '';
			
			if($_GET['pagina'] > 1){
				$Paginado .= '<a class="next-page" href="?'.procesar_argumentos($URI, null, array('pagina' => ($_GET['pagina'] - 1))).'">&laquo;</a> ';
			}
			
			for($I = 1; $I <= ceil($Cantidad_Registros / $Cantidad_Paginar); $I++){
				if($I == $_GET['pagina']){
					$Paginado .= '<span class="next-page disabled">'.$I.'</span> ';
				}
				else{										
					if($I == 1){
						$Paginado .='<a class="next-page" href="?'.procesar_argumentos($URI, null, array('pagina' => ($I))).'">'.$I.'</a> ';
					}
					else if($I == ceil($Cantidad_Registros / $Cantidad_Paginar)){
						$Paginado .='<a class="next-page" href="?'.procesar_argumentos($URI, null, array('pagina' => ($I))).'">'.$I.'</a> ';
					}
					else if($I == ($_GET['pagina'] + 5) or $I == ($_GET['pagina'] - 5)){
						$Paginado .= '... ';
					}
					else if($I > ($_GET['pagina'] + 5) or $I < ($_GET['pagina'] - 5)){
						// No se muestra las paginas entre el rango
					}								
					else{
						$Paginado .='<a class="next-page" href="?'.procesar_argumentos($URI, null, array('pagina' => ($I))).'">'.$I.'</a> ';
					}
				}
			}
				
			if($_GET['pagina'] < ceil($Cantidad_Registros / $Cantidad_Paginar)){
				$Paginado .= '<a class="next-page" href="?'.procesar_argumentos($URI, null, array('pagina' => ($_GET['pagina'] + 1))).'">&raquo;</a>';
			}
		}
		else{
			$Paginado = '';
		}

		return $Paginado;
	}
	
	function get_url_parameters($url){
		$url = @parse_url($url);
		$querys = explode('&', $url['query']);

		if(is_array($querys)){
			foreach($querys as $query){
				list($key, $valor) = explode('=', $query);
				
				$parameters[$key] = $valor;
			}
			
			if(is_array($parameters)){
				return $parameters;
			}
		}
		
		return false;
	}
	
	function process_parameters($Argumentos, $Eliminar = null, $Agregar = null){		
		if($Eliminar != null){
			if(!is_array($Eliminar)){
				return false;
			}
			else{
				foreach($Eliminar as $Elemento_Eliminar){
					$Elementos_Eliminar[$Elemento_Eliminar] = string;
				}
				
				$Eliminar = $Elementos_Eliminar;
			}
		}
		else{
			$Eliminar = array();
		}

		if($Agregar != null){
			if(is_array($Agregar)){
				foreach($Agregar as $Clave => $Valor){
					$Elementos_Agregar[] = $Clave.'='.$Valor;
				}
				
				$Elementos_Agregar = implode('&', $Elementos_Agregar);
			}
			else{
				return false;
			}	
		}			
		
		if(is_object($Argumentos)){
			$Elementos = get_object_vars($Argumentos);
		}
		else if(is_array($Argumentos)){
			$Elementos =& $Argumentos;
		}
		else{
			parse_str($Argumentos, $Elementos);
		}
	
		$Elementos = array_diff_key($Elementos, $Eliminar);
		
		if(!empty($Agregar) and is_array($Agregar)){
			$Elementos = array_diff_key($Elementos, $Agregar);
		}
		
		foreach($Elementos as $Clave => $Valor){			
			$Salida[] = $Clave.'='.$Valor;
		}
		
		if(!empty($Elementos_Agregar)){
			$Elementos_Agregar = '&'.$Elementos_Agregar;
		}
		else{
			$Elementos_Agregar = '';
		}
		
		return @implode('&', $Salida).$Elementos_Agregar;
	}
	
	function get_whatsapp_api($numero){
		if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])){
			return 'https://api.whatsapp.com/send?phone='.str_replace(array(' ', '+', '(', ')'), '', $numero);
		}
		
		return 'https://web.whatsapp.com/send?phone='.str_replace(array(' ', '+', '(', ')'), '', $numero);
	}
	
?>