<?php
global $PHP_SELF;
$script = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1)."?";
if (!strpos($script,"?")) {
	$script.="?";
//		}elseif (substr($script,strlen($script)-1)!="&" && !substr($script,strlen($script)-1)=="?"){
}elseif (substr($script,strlen($script)-1)!="&"){
	$script.="&";
}
debug($script);
?>