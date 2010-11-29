<?php

//Lib file containing functions for numbers and dialogue

function speakify($number){
	if (($number < 0) || ($number > 999999999999999)){
		debug("Number to be speakified is out of range");
		return $number;
	}
	$Pn = floor($number / 1000000000000);  /* Trillions (peta) */
	$number -= $Pn * 1000000000000;
	$Tn = floor($number / 1000000000);  /* Billions (tera) */
	$number -= $Tn * 1000000000; 
	$Gn = floor($number / 1000000);  /* Millions (giga) */ 
	$number -= $Gn * 1000000; 
	$kn = floor($number / 1000);     /* Thousands (kilo) */ 
	$number -= $kn * 1000; 
	$Hn = floor($number / 100);      /* Hundreds (hecto) */ 
	$number -= $Hn * 100; 
	$Dn = floor($number / 10);       /* Tens (deca) */ 
	$n = $number % 10;               /* Ones */ 

	$res = ""; 

	if ($Pn){ 
		$res .= (empty($res) ? "" : " ") . speakify($Pn) . " trillion";
		if ($Tn || $Gn || $kn || $Hn){
			$res.=",";
		}
	}

	if ($Tn){ 
		$res .= (empty($res) ? "" : " ") . speakify($Tn) . " billion";
		if ($Gn || $kn || $Hn){
			$res.=",";
		}
	}	
	
	if ($Gn){ 
		$res .= (empty($res) ? "" : " ") . speakify($Gn) . " million";
		if ($kn || $Hn){
			$res.=",";
		}
	}

	if ($kn){ 
		$res .= (empty($res) ? "" : " ") . speakify($kn) . " thousand";
		if ($Hn){
			$res.=",";
		}
	}

	if ($Hn){ 
		$res .= (empty($res) ? "" : " ") . speakify($Hn) . " hundred"; 
	}

	$ones = array("", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"); 
	$tens = array("", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"); 

	if ($Dn || $n){
		if (!empty($res)) { 
			$res .= " and "; 
		}
		if ($Dn < 2){ 
			$res .= $ones[$Dn * 10 + $n]; 
		} else {
			$res .= $tens[$Dn];
            if ($n){ 
				$res .= "-" . $ones[$n]; 
			}
		} 
	}
	
	if (empty($res)){ 
		$res = "zero"; 
	}
	return $res; 
}

function vagueify($val){
	$chars = strlen($val);
	$div=1;
	for ($i=1; $i<$chars; $i++){
		$div=$div*10;
	}
	$ret=array();
	$low = (floor($val/$div))*$div;
	$high = (ceil($val/$div))*$div;
	$mid = (($low/$div) + 0.5)*$div;
	if ($val>$mid){
		$ret['low']=$mid;
		$ret['high']=$high;
	} else {
		$ret['low']=$low;
		$ret['high']=$mid;
	}
	return $ret;
}

?>