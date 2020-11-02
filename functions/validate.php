<?php	
	
	function validate_fields($campos, $defecto, $omitir = null){
		if(!is_array($defecto)){
			return false;
		}	
			
		$_campos = $defecto;
		$redistribucion = $validacion = array();
			
		foreach($_campos as $clave => $campo){
			if(!isset($campo['requerido'])){
				$campo['requerido'] = false;
			}
			
			if(isset($campo['meta']) and !empty($campo['meta'])){
				$clave = $campo['meta'];
			}
			
			$redistribucion[$clave] = $campo;
		}
			
		$_campos = $redistribucion;

		foreach((array) $campos as $campo => $valor){
			$validar = true;				
			
			if(isset($_campos[$campo]) and is_array($_campos[$campo]['requerido'])){
				$requerido = true;
				
				foreach($_campos[$campo]['requerido'] as $necesario => $resultado){
					if(isset($campos[$necesario]) and $campos[$necesario] != $resultado){
						$requerido = false;
					}
				}
				
				$_campos[$campo]['requerido'] = $requerido;					
			}
			
			if(isset($_campos[$campo]) and $_campos[$campo]['requerido']){
				$comprobar = true;
				
				if(!is_null($omitir) and is_array($omitir)){
					if(in_array($campo, $omitir)){
						$comprobar = false;
					}
				}
				
				if($comprobar){
					if($_campos[$campo]['tipo'] == 'file'){
						if(empty($valor['name'][0])){
							$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe contener al menos un archivo.';
						}
					}
					else{
						if(empty($valor)){
							$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> es necesario.';
							
							$validar = false;
						}
					}
				}
			}
			else{
				if(empty($valor)){
					$validar = false;
				}
			}
				
			if($validar){
				if(isset($_campos[$campo]) and !empty($valor)){
					switch($_campos[$campo]['tipo']){
						case 'email':
							if(!is_email($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser una direcci&oacuten de correo.';
							}
							break;
						case 'url':
							if(!is_url($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser una URL v&aacute;lida.';
							}
							break;
						case 'time':
							if(!is_time($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser un horario v&aacute;lido.';
							}
							break;
						case 'date':
							if(!is_date($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser una fecha v&aacute;lida.';
							}
							break;
						case 'datetime':
							if(!is_dateformat($valor, 'd/m/Y H:i')){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser una fecha y horario v&aacute;lidos.';
							}
							break;
						case 'number':
							if(!is_numeric($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser solo n&uacute;meros.';
							}
							break;
						case 'dni':
							if(!is_dni($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser un n&uacute;mero de documento v&aacute;lido.';
							}
							break;
						case 'cui':
							if(!is_cui($valor)){
								$validacion[] = '<strong>'.$_campos[$campo]['texto'].'</strong> debe ser un n&uacute;mero de CUIT/CUIL v&aacute;lido.';
							}
							break;
					}
				}
			}
		}
		
		if(empty($validacion)){
			return true;
		}
		else{			
			return $validacion;
		}
	
		return false;
	}
	
	function is_cbu($cbu){
		$entidades = array(
			'005' => 'The Royal Bank of Scotland N.V.',
			'007' => 'Banco de Galicia y Buenos Aires S.A.',
			'011' => 'Banco de la Naci&oacute;n Argentina',
			'014' => 'Banco de la Provincia de Buenos Aires',
			'015' => 'Industrial and Commercial Bank of China S.A.',
			'016' => 'Citibank N.A.',
			'017' => 'BBVA Banco Franc&eacute;s S.A.',
			'018' => 'The Bank of Tokyo-Mitsubishi UFJ, LTD.',
			'020' => 'Banco de la Provincia de C&oacute;rdoba S.A.',
			'027' => 'Banco Supervielle S.A.',
			'029' => 'Banco de la Ciudad de Buenos Aires',
			'030' => 'Central de la Rep&uacute;blica Argentina',
			'034' => 'Banco Patagonia S.A.',
			'044' => 'Banco Hipotecario S.A.',
			'045' => 'Banco de San Juan S.A.',
			'046' => 'Banco do Brasil S.A.',
			'060' => 'Banco de Tucum&aacute;n S.A. plazoleta mitre',
			'065' => 'Banco Municipal de Rosario',
			'072' => 'Banco Santander R&iacute;o S.A.',
			'083' => 'Banco del Chubut S.A.',
			'086' => 'Banco de Santa Cruz S.A.',
			'093' => 'Banco de la Pampa Sociedad de Econom&iacute;a Mixta',
			'094' => 'Banco de Corrientes S.A.',
			'097' => 'Banco Provincia del Neuqu&eacute;n S.A.',
			'143' => 'Banco Interfinanzas S.A.',
			'150' => 'HSBC Bank Argentina S.A.',
			'165' => 'JP Morgan Chase Bank NA (Sucursal Buenos Aires)',
			'191' => 'Banco Credicoop Cooperativo Limitado',
			'198' => 'Banco de Valores S.A.',
			'247' => 'Banco Roela S.A.',
			'254' => 'Banco Mariva S.A.',
			'259' => 'Banco Ita&uacute; Argentina S.A.',
			'262' => 'Bank of America National Association',
			'266' => 'BNP Paribas',
			'268' => 'Banco Provincia de Tierra del Fuego',
			'269' => 'Banco de la Rep&uacute;blica Oriental del Uruguay',
			'277' => 'Banco S&aacute;enz S.A.',
			'281' => 'Banco Meridian S.A.',
			'285' => 'Banco Macro S.A.',
			'295' => 'American Express Bank LTD. S.A.',
			'299' => 'Banco Comafi S.A.',
			'300' => 'Banco de Inversi&oacute;n y Comercio Exterior S.A.',
			'301' => 'Banco Piano S.A.',
			'305' => 'Banco Julio S.A.',
			'309' => 'Nuevo Banco de la Rioja S.A.',
			'310' => 'Banco del Sol S.A.',
			'311' => 'Nuevo Banco del Chaco S.A.',
			'312' => 'MBA Lazard Banco de Inversiones S.A.',
			'315' => 'Banco de Formosa S.A.',
			'319' => 'Banco CMF S.A.',
			'321' => 'Banco de Santiago del Estero S.A.',
			'322' => 'Banco Industrial S.A.',
			'325' => 'Deutsche Bank S.A.',
			'330' => 'Nuevo Banco de Santa Fe S.A.',
			'331' => 'Banco Cetelem Argentina S.A.',
			'332' => 'Banco de Servicios Financieros S.A.',
			'336' => 'Banco Bradesco Argentina S.A.',
			'338' => 'Banco de Servicios y Transacciones S.A.',
			'339' => 'RCI Banque S.A.',
			'340' => 'BACS Banco de Cr&eacute;dito y Securitizaci&oacute;n S.A.',
			'341' => 'M&aacute;s Ventas S.A.',
			'386' => 'Nuevo Banco de Entre R&iacute;os S.A.',
			'389' => 'Banco Columbia S.A.',
			'405' => 'Ford Credit Compa&ntilde;&iacute;a Financiera S.A.',
			'406' => 'Metr&oacute;polis Compa&ntilde;&iacute;a Financiera S.A.',
			'408' => 'Compa&ntilde;&iacute;a Financiera Argentina S.A.',
			'413' => 'Montemar Compa&ntilde;&iacute;a Financiera S.A.',
			'415' => 'Multifinanzas Compa&ntilde;&iacute;a Financiera S.A.',
			'428' => 'Caja de Cr&eacute;dito Coop. La Capital del Plata LTDA.',
			'431' => 'Banco Coinag S.A.',
			'432' => 'Banco de Comercio S.A.',
			'434' => 'Caja de Cr&eacute;dito Cuenca Coop. LTDA.',
			'437' => 'Volkswagen Credit Compa&ntilde;&iacute;a Financiera S.A.',
			'438' => 'Cordial Compa&ntilde;&iacute;a Financiera S.A.',
			'440' => 'Fiat Cr&eacute;dito Compa&ntilde;&iacute;a Financiera S.A.',
			'441' => 'GPAT Compa&ntilde;&iacute;a Financiera S.A.',
			'442' => 'Mercedes-Benz Compa&ntilde;&iacute;a Financiera Argentina S.A.',
			'443' => 'Rombo Compa&ntilde;&iacute;a Financiera S.A.',
			'444' => 'John Deere Credit Compa&ntilde;&iacute;a Financiera S.A.',
			'445' => 'PSA Finance Argentina Compa&ntilde;&iacute;a Financiera S.A.',
			'446' => 'Toyota Compa&ntilde;&iacute;a Financiera de Argentina S.A.',
			'448' => 'Finandino Compa&ntilde;&iacute;a Financiera S.A.',
			'992' => 'Provincanje S.A.'
		);
		
		$patrones = array(7139713, 3971397139713);	
		
		$bloque = array(str_split(substr($cbu, 0, 7)), str_split(substr($cbu, 8, 13)));
		$digito = array(substr($cbu, 7, 1), substr($cbu, 21, 1));		
		$verificacion = array(0, 0);

		if(preg_match('/[0-9]{22}/', $cbu)){
			foreach($patrones as $clave => $patron){
				foreach(str_split($patron) as $indice => $valor){
					$suma = $bloque[$clave][$indice] * $valor;
					
					$verificacion[$clave] = $verificacion[$clave] + $suma;
				}
				
				if($verificacion[$clave] > 0){
					$verificacion[$clave] = 10 - substr($verificacion[$clave], -1);
					
					if($verificacion[$clave] !== 0 and $verificacion[$clave] == $digito[$clave]){
						$verificacion[$clave] = true;
					}
				}
			}
		}
		
		if(is_array($verificacion) and !empty($verificacion)){
			if($verificacion[0] === true and $verificacion[1] === true){
				$banco = substr($cbu, 0, 3);
				
				
				return array('entidad' => $entidades[$banco], 'banco' => $banco, 'sucursal' => substr($cbu, 3, 4), 'cuenta' => substr($cbu, 8, 13));
			}	
		}
		
		return false;
	}
	
	function is_dni($valor){ 
		$valor = str_replace(array(' ', '-', '.'), '', trim($valor));
		
		if(strlen($valor) >= 8){
			if(strlen($valor) <= 9){
				return true;
			}
		}
		
		return false;
	}
	
	function is_cui($cuit){
		$coeficiente = array(5, 4, 3, 2, 7, 6, 5, 4 ,3, 2);	
		$recodificacion = '';
		
		#Se explora caracter por caracter y solo se recodifican los que sean caracteres numericos.
		
		for($i = 0; $i < strlen($cuit); $i++) { 
			$ascii = ord(substr($cuit, $i, 1));
			
			if($ascii >= 48 and $ascii <= 57){
				$recodificacion = $recodificacion . substr($cuit, $i, 1);
			}
		}

		if(strlen($recodificacion) === 11){	
			$resultado = false;
			$sumador = 0;
			
			#Se obtiene el digito 11 (Digito verificador)
			
			$verificador = substr($recodificacion, 10, 1);

			for($i = 0; $i <= 9; $i++){
				#Se genera una suma donde cada numero sera multiplicado por su coediciente.
				
				$sumador = $sumador + (substr($recodificacion, $i, 1)) * $coeficiente[$i];			
			}
			
			$modulo = ($sumador % 11);
			
			if($modulo === 0 and $modulo === intval($verificador)){
				$resultado = true;
			}
			else{			
				If(intval($verificador) === (11 - $modulo)){
					$resultado = true;
				}
			}
			
			If($resultado){
				return substr($recodificacion, 0, 2) . '-' . substr($recodificacion, 2, 8) . '-' . substr($recodificacion, 10, 1);
			}
		} 
		
		return false;
	}
		
	function is_id($id){
		if($id > 0 and ctype_digit((string) $id)){
			return true;
		}
		
		return  false;
	}
	
?>