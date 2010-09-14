<?php
// translator ready
// addnews ready
// mail ready
//This is a data caching library intended to lighten the load on lotgd.net
//use of this library is not recommended for most installations as it raises
//the issue of some race conditions which are mitigated on high volume
//sites but which could cause odd behavior on low volume sites, with out
//offering much if any advantage.

//basically the idea behind this library is to provide a non-blocking
//storage mechanism for non-critical data.

$datacache = array();
$datacachefilepath = "";
$checkedforolddatacaches = false;
define("DATACACHE_FILENAME_PREFIX","datacache_");

function datacache($name,$duration=60){
	global $datacache;
	if (getsetting("usedatacache",0)){
		if (isset($datacache[$name])){
			// we've already loaded this data cache this page hit and we
			// can simply return it.
			// debug($name." is already in memory - returning");
			return $datacache[$name];
		}else{
			//we haven't loaded this data cache this page hit.
			$fullname = makecachetempname($name);
			if (@filemtime($fullname) > (time()-$duration)){
				//the cache file *does* exist, and is not overly old.
				$fullfile = @file_get_contents($fullname);
				if ($fullfile > ""){
					$datacache[$name] = @unserialize($fullfile);
					// debug($name." was looked up successfully");
					return $datacache[$name];
				}else{
					// debug($name." exists, but was empty");
					return false;
				}
			}
		}
		// debug("Failed to look up the ".$name." datacache file.");
	}
	// The field didn't exist, or it was too old.
	return false;
}

//do NOT send simply a false value in to array or it will bork datacache in to
//thinking that no data is cached or we are outside of the cache period.
function updatedatacache($name,$data){
	global $datacache;
	if (getsetting("usedatacache",0)){
		$fullname = makecachetempname($name);
		$datacache[$name] = $data; //serialize($array);
		$fp = @fopen($fullname,"w");
		if ($fp){
			if (!fwrite($fp,serialize($data))){
				// debug("FAILED to write to ".$fullname);
			}else{
				// debug("Wrote data to ".$fullname);
			}
			fclose($fp);
		}else{
			// debug("FAILED to open ".$fullname." for writing.");
		}
		return true;
	}
	return false;
}

//we want to be able to invalidate data caches when we know we've done
//something which would change the data.
function invalidatedatacache($name,$full=false){
	global $datacache;
	if (getsetting("usedatacache",0)){
		if(!$full) $fullname = makecachetempname($name);
		else $fullname = $name;
		if (file_exists($fullname)){
			// debug("Unlinking file ".$fullname);
			@unlink($fullname);
		} else {
			// debug("Cannot unlink file ".$fullname);
		}
		unset($datacache[$name]);
	}
}


//Invalidates *all* caches, which contain $name at the beginning of their filename.
function massinvalidate($name,$dir=false) {
	if (getsetting("usedatacache",0)){
		//$name = DATACACHE_FILENAME_PREFIX.$name;
		global $datacachefilepath;
		if ($datacachefilepath=="") $datacachefilepath = getsetting("datacachepath","/tmp");
		if ($dir){
			$datacachefilepath.="/".$dir;
		}
		$cachepath = dir($datacachefilepath);
		// debug("Trying to invalidate ".$name);
		while(false !== ($file = $cachepath->read())) {
			if (strpos($file, $name) !== false) {
				invalidatedatacache($cachepath->path."/".$file,true);
				// debug("Invalidated ".$file);
			}
		}
		$cachepath->close();
	}
}


function makecachetempname($name){
	//one place to sanitize names for data caches.
	global $datacache, $datacachefilepath,$checkedforolddatacaches;
	if ($datacachefilepath=="") $datacachefilepath = getsetting("datacachepath","/tmp");
	$path = pathinfo($name);
	if (!file_exists($datacachefilepath."/".$path['dirname'])){
		@mkdir($datacachefilepath."/".$path['dirname'],0777,1);
	}
	$fullname = $datacachefilepath."/".$name;
	$fullname = preg_replace("'//'","/",$fullname);
	$fullname = preg_replace("'\\\\'","\\",$fullname);
	if ($checkedforolddatacaches==false){
		$checkedforolddatacaches=true;
	}
	// echo($fullname);
	return $fullname;
}

?>
