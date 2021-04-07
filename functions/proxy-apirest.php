<?php

	// $proxy = new proxyApiRest('https://domain.com/api-path/', '/file/folder/to/call/api/');
	// $proxy -> run();

	class proxyApiRest{
		public $url = '';
		public $headers = array();
		public $method = '';
		public $data = '';
		public $response = '';
		
		function __construct($url, $path = ''){
			if(!empty($url)){
				$this -> url = substr($url, 0, strrpos($url, '/')) .'/'. str_replace($path, '', $_SERVER['REQUEST_URI']);
			}
			
			$this -> method = strtoupper($_SERVER["REQUEST_METHOD"]);
			$this -> headers = $this -> getRequestHeaders();
		}
		
		public function getRequestHeaders(){
			$headers = array();
			
			$vars = array(
				'Content-Type: '.((!empty($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : 'application/json')
			);
				
			foreach($_SERVER as $key => $value){
				if(substr($key, 0, 5) <> 'HTTP_'){
					continue;
				}
				
				$headers[] = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5))))).': '.$value;
			}	
				
			return array_merge($vars, $headers);
		}
		
		public function run(){
			if(empty($this -> url) or empty($this -> method)){
				$this -> send('400');
			}
			
			$conection = curl_init($this -> url);
			
			if($this -> method == 'POST'){
				$this -> data = http_build_query($_POST);			
				$this -> headers[] = 'Content-Length: '.strlen($this -> data);
				
				curl_setopt($conection, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($conection, CURLOPT_POSTFIELDS, $this -> data);
			}
			else if($this -> method == 'PUT'){
				$this -> data = substr(file_get_contents('php://input'), strlen($this -> url));
				
				curl_setopt($conection, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($conection, CURLOPT_POSTFIELDS, $this -> data);
			}
			else if($this -> method == 'PATCH'){
				
			}
			else if($this -> method == 'DELETE'){
				$this -> data = substr(file_get_contents('php://input'), strlen($this -> url));
				
				curl_setopt($conection, CURLOPT_CUSTOMREQUEST, 'DELETE');
			}
			else if($this -> method == 'GET'){
				curl_setopt($conection, CURLOPT_CUSTOMREQUEST, 'GET');
			}	
			
			curl_setopt($conection, CURLOPT_HTTPHEADER, $this -> headers);
			curl_setopt($conection, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36');
			curl_setopt($conection, CURLOPT_RETURNTRANSFER, true);
			
			$this -> response = curl_exec($conection);
			$httpcode = curl_getinfo($conection, CURLINFO_HTTP_CODE);

			curl_close($conection);

			$this -> send($httpcode);
		}
		
		private function send($code = '200'){
			$codes = array(
				'200' => 'HTTP/1.0 200 Ok',
				'201' => 'HTTP/1.0 201 Created',
				'202' => 'HTTP/1.0 202 Accepted',
				'203' => 'HTTP/1.0 203 Non-Authoritative Information',
				'301' => 'HTTP/1.0 301 Moved Permanently',
				'400' => 'HTTP/1.0 400 Bad Request',
				'401' => 'HTTP/1.0 401 Unauthorized',
				'404' => 'HTTP/1.0 404 Not Found',
				'409' => 'HTTP/1.0 409 Conflict',
				'412' => 'HTTP/1.0 412 Precondition Failed',
				'500' => 'HTTP/1.0 500 Internal Server Error',
				'503' => 'HTTP/1.0 503 Service Unavailable',
			); 
			
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
			header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
			header($codes[$code]);
			header('Content-type: application/json');
			header('Content-length: '.@strlen($this -> response));

			if($code == '200'){
				echo($this -> response);
			} else {
				echo('Proxy response code: '.$code);
			}
			
			exit;
		}
	}

?>