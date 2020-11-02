<?php	

	function age($fecha){
		list($ano, $mes, $dia) = explode('-', $fecha);
		
		$ano_diferencia  = date('Y') - $ano;
		$mes_diferencia = date('m') - $mes;
		$dia_diferencia   = date('d') - $dia;
		
		if($dia_diferencia < 0 or $mes_diferencia < 0){
			$ano_diferencia--;
		}
		
		return $ano_diferencia;
	}	
	
	function percentage($Total, $Valor, $Redondear = 0, $Calculo = false){
		if($Calculo){
			//devuelve el porcentaje
			return round(((100 * $Valor) / $Total), $Redondear);									
		}
		else{
			//devuelve el valor del numero
			return round((($Valor * $Total) / 100), $Redondear);
		}
	}
	
?>