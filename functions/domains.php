<?php
	
	function get_location($retorno = null){				
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}	 
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		//ACTUALIZACION: Es posible descargar las locacones de las IP. El servicio dispone de archivos SQL descargable.
			
		if($carga = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=6e523df05b1024ef9a6a19c1a877ff6a859ff97ec487ce0cb9c7a8114fc2bae1&ip=".$ip."&format=json", false, stream_context_create(array('http' => array( 'timeout' => 15))))){
			$locaciones = json_decode($carga);
			
			if($locaciones -> statusCode !== 'ERROR'){	
				$valores = array('pais' => $locaciones -> countryName, 'estado' => $locaciones -> regionName, 'ciudad' => $locaciones -> cityName, 'ip' => $locaciones -> ipAddress);	
				
				if(is_null($retorno)){
					return (object) $valores;
				}
				else if(!empty($retorno) and isset($valores[$retorno])){
					return $valores[$retorno];
				}
			}
		}
		
		return false;
	}
			
	function get_ip(){
		if(isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else if(isset($_SERVER ['HTTP_VIA'])){
			$ip = $_SERVER['HTTP_VIA'];
		}
		else if(isset($_SERVER['REMOTE_ADDR'])){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else{
			$ip = null;
		}
		
		return $ip;
	}

	function get_domain($url){
		if($url == null){
			$url = $_SERVER['HTTP_HOST'];
		}
  
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';

		if(empty($domain)){
			if(preg_match('/^(([a-z0-9]([-a-z0-9]*[a-z0-9]+)?){1,63}\.)+[a-z]{2,6}/i', strtolower($pieces['path']))){
				$domain = preg_replace('#^https?://#', '', strtolower($pieces['path']));			
				$domain = preg_replace('#^www.#', '', $domain);
			}
		}
   
		if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		
		return false;
	}

	function get_subdomains($url = null){
		if($url == null and isset($_SERVER['HTTP_HOST'])){
			$url = $_SERVER['HTTP_HOST'];
		}
	  
		if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)){
			$Protocolo = 'http://';
			
			if(isset($_SERVER['SERVER_PROTOCOL'])){
				$Protocolo = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http://' : 'https://';
			}

			$pieces = parse_url($Protocolo.$url);
		}
		else{
			$pieces = parse_url($url);
		}

		$domain = isset($pieces['host']) ? $pieces['host'] : '';
	  
		if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {    
			$url = preg_replace(array('/http:\/\//', '/https:\/\//'), '', $url);
		
			if($regs['domain'] !== $url){
				$Subdominios = explode('.'.$regs['domain'], $url);
				$Subdominios = explode('.', $Subdominios[0]);
				$Subdominios = array_slice($Subdominios, 0, count($Subdominios));
			
				return $Subdominios;
			}
		}
	  
		return false;
	}
		
	function is_url($url){
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
	
	function is_ip($ip){
		if(preg_match('/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])'.'(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/', $ip)){
			return true;
		}
		
		return false;
	}
		
	function is_domain($dominio){
		$dominio = strtolower($dominio);		
		$dominio = preg_replace('#^https?://#', '', $dominio);			
		
		if(preg_match('/^(([a-z0-9]([-a-z0-9]*[a-z0-9]+)?){1,63}\.)+[a-z]{2,6}/i', strtolower($dominio))){
			return true;
		}
		
		return false;
	}

?>