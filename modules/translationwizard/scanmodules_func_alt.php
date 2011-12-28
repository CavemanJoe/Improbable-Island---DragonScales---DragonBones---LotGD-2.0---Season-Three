<?
function scanfile($lookfor) {
require_once("lib/pullurl.php");
$posi=strrpos($lookfor,"/");
$name=substr($lookfor,$posi+1,strlen($lookfor)-$posi-5);
if (!$posi) $name=substr($lookfor,0,strrpos($lookfor,"."));
$i=0;debug($name);
if (strstr($lookfor,"modules")) $name="module-".$name;

if ($lookfor) $scanarray=file("$lookfor");
$ausgabe=array();
while (list($key,$val) = each ($scanarray))
	{
	if (strstr($val,"//")) $val=substr($val,0,strpos($val,"//"));
	if (strstr($val,"tlschema") && !strstr($val, "if"))
		{
		$backup=$name;
		$name=substr(stripslashes($val),strpos(stripslashes($val),"\"")+1);
		$name=substr($name,0,strpos($name,"\""));
		if (!$name) $name=$backup;
		}
	if (strstr($val,"translate_inline"))
		{
		$val=substr($val,strpos($val,"translate_inline"));
		$ab=substr(stripslashes($val),strpos(stripslashes($val),"\"")+1);
		$cutzwei=substr($ab,0,strpos($ab,"\""));
		$a=array($cutzwei,$name);
		if ($cutzwei)
			{
			$ausgabe[$i]=join("||||", $a);
			$i++;
			}
		if (strstr($ab,"translate_inline"))
			{
			//debug($ab);
			$ab=substr($ab,strpos($ab,"translate_inline"));
			$ab=substr(stripslashes($ab),strpos(stripslashes($ab),"\"")+1);
			$cutzwei=substr($ab,0,strpos($ab,"\""));
			$a=array($cutzwei,$name);
			if ($cutzwei)
				{
				$ausgabe[$i]=join("||||", $a);
				$i++;
				}
			}	
		}
	
	if ((strstr($val,"output")&& !strstr($val,"output_notl")) || strstr($val,"page_header"))
		{
		if (!strstr($val,"rawoutput")&& !strstr($val,"array"))
			{
			if (strstr ($val,"page_header")) $val=substr($val,strpos($val,"page_header"));
			if (strstr ($val,"output")) $val=substr($val,strpos($val,"output"));
			$test=substr($val,strpos($val,"(")+1);
			$test=substr($test,strpos($test,"\"")+1);
			(strpos($test,"\"")?$test=true:$test=false);
			if ($test)
				{
				$val=str_replace("\\\"","<tagfor>",$val);
				$cuteins=substr($val,strpos($val,"\"")+1);
				$cutzwei=str_replace("<tagfor>","\"",stripslashes(substr($cuteins,0,strpos($cuteins,"\""))));
				if ($cutzwei)
					{
					$a=array($cutzwei,$name);
					$ausgabe[$i]=join("||||", $a);
					$i++;
					}
				}
			}
		}
	if (strstr($val,"addnav")&& !strstr($val,"addraw")&&!strstr($val,"\$args"))
		{
		$val=substr($val,strpos($val,"addnav"));
		$cuteins=substr($val,strpos($val,"\"")+1);
		$cutzwei=stripslashes(substr($cuteins,0,strpos($cuteins,"\"")));
		if ($cutzwei)
			{
			$a=array($cutzwei,$name);
			$ausgabe[$i]=join("||||", $a);
			$i++;
			}
		}		
	if (strstr($val,"addnews")&& !strstr($val,"addraw"))
		{

		$val=substr($val,strpos($val,"addnews"));
		$cuteins=substr($val,strpos($val,"\"")+1);
		$cutzwei=stripslashes(substr($cuteins,0,strpos($cuteins,"\"")));
		if ($cutzwei)
			{
			$a=array($cutzwei,$name);
			$ausgabe[$i]=join("||||", $a);
			$i++;
			}
		}	
	}
return array_unique($ausgabe);	
}

function insertfile($delrows,$languageschema,$serialized=false) {
	if (is_array($delrows))  //setting for any intexts you might receive
		{
		$insertrows = $delrows;
		}else
		{
		if ($delrows) $insertrows  = array($insertrows);
		else 
			{
			$insertrows = array();
			}
		}
	while (list($key,$val) = each ($insertrows))
		{
		if ($serialized) {
			$val=unserialize(rawurldecode($val));
			}else $val = split("[||||]", $val);
		$sql="Insert ignore into ".db_prefix("untranslated")." Values ('".addslashes($val[0])."','$languageschema','".addslashes($val[4])."');";
		db_query($sql);
		}
}
?>