<?php
	
	function array_read($array, $path){
		if(($posicion = strpos($path, '/')) !== false){
			$key = substr($path, 0, $posicion);
			
			$resto = substr($path, $posicion + 1); 

			if(isset($array[$key])){
				return array_read($array[$key], $resto);
			}
		} 
		else {
			$key = $path;
			
			if(isset($array[$key])){
				return $array[$key];
			}
		}
	}	
	
	function array_encode($array){
		if(is_array($array)){
			if($array = serialize($array)){
				if($array = base64_encode($array)){
					return $array;
				}
			}
		}
		
		return false;
	}
	
	function array_decode($array){
		if($array = base64_decode($array)){
			if($array = unserialize($array)){
				if(is_array($array)){
					return $array;
				}
			}
		}
		
		return false;
	}

	function array_object($element) {
		$redistribution = new stdClass();
		
		if(!(is_array($element) or is_object($element))){
            $redistribution = $element;
        }
		else{
            foreach($element as $key => $value){
                $redistribution-> {$key} = array_object($value);
            }
        }
		
        return $redistribution;	
	}	
	   
	function object_array($element){
        if(!(is_array($element) or is_object($element))){
            $redistribution = $element;
        }
		else{
            $redistribution = array();
			
			foreach($element as $key => $value){
                $redistribution[$key] = object_array($value);
            }
        }
		
        return $redistribution;
    }
	
	function empty_object($object){
		foreach($object as $item){
			return false;
		}

		return true;
	}

?>