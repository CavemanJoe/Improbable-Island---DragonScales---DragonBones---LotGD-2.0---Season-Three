<?php
// This method has been shamelessly copied and adapted from the method used
// by Python(2.6.4)'s random.normalvariate function.

// Per that source's comments, the method comes from this publication:
// Kinderman, A.J. and Monahan, J.F.
// "Computer generation of random variables using the ratio of uniform deviates"
// ACM Trans Math Software, 3, (1977)
// pp257-260.

// Adaptation by FunnyMan3595 (Charlie Nolan), February 2010.
// This code is hereby released to the public domain.
// Use, modify, and distribute at will.

// 4 * e^(-0.5)/sqrt(2)
define("BELL_CURVE_MAGIC_CONSTANT", 1.7155277699214135);

// $min and $max are the 5th and 95th percentiles (respectively)
// Therefore, they are this many sigmas (=standard deviations) from the mean.
// This lets us calculate what sigma is.
// Pulled from http://en.wikipedia.org/wiki/Normal_distribution
define("BELL_CURVE_SIGMAS_TO_90_PERCENT", 1.644853626951);

function bell_rand($min=false, $max=false){
	if ($min===false && $max===false) {
		//no value passed, assume 0 min, and 1 max.
		$min=0;
		$max=1;
	}
	if ($max===false){ // here got something mixed up in the previous versions
		//only one value passed, assume it is the max.
		$max = $min;
		$min = 0;
	}
	if($min>$max){
		//min is bigger than max, switch.
		$x = $max;
		$max = $min;
		$min = $x;
	}
	if (($min-$max)==0){
		//equal values, return one of them.
		return $min;
	}

	$mean = ($max + $min) / 2;
	$distance = $max - $mean;
	// See note on BELL_CURVE_SIGMAS_TO_90_PERCENT.
	$sigma = $distance / BELL_CURVE_SIGMAS_TO_90_PERCENT;

	// In 60,000 tests, this loop never ran more than 8 times.
	// It appears to have about a 75% chance of terminating per run,
	// so once every billion or so calls, it'll hit 16.
	do {
		$u1 = mt_rand() / mt_getrandmax();
		$u2 = 1 - (mt_rand() / mt_getrandmax());
		$z = BELL_CURVE_MAGIC_CONSTANT * ($u1 - 0.5) / $u2;
		$zz = $z * $z / 4.0;
	} while ($zz > -log($u2));

	return $mean + $z * $sigma;
}

?>