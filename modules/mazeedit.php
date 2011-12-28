<?PHP
//V1.42 Minor Spelling Changes by DaveS
function mazeedit_getmoduleinfo(){
	$info = array(
		"name"=>"Maze Editor",
		"version"=>"1.42",
		"author"=>"`#Lonny Luberts",
		"category"=>"PQcomp",
		"download"=>"http://www.pqcomp.com/modules/mydownloads/visit.php?cid=3&lid=28",
		"vertxtloc"=>"http://www.pqcomp.com/",
		"prefs"=>array(
			"Maze Editor User Preferences,title",
			"canedit"=>"User can edit a maze,bool|0",
			"mazeedit"=>"Maze Edit|",
		),
	);
	return $info;
}

function mazeedit_install(){
	module_addhook("village");
	return true;
}

function mazeedit_uninstall(){
	return true;
}
function mazeedit_dohook($hookname,$args){
	global $session;
	if (get_module_pref("canedit")) {
		addnav('Other');
		addnav("Maze Editor","runmodule.php?module=mazeedit&op=reload");
	}
	return $args;
}
function mazeedit_run(){
//old code below here
global $session;
$op = httpget('op');
$piece = httpget('piece');
$umazeedit = get_module_pref('mazeedit');
page_header("Maze Editor");
output("`c`b`&Maze Editor`0`b`c");
output("`cIt is up to you to make a maze that connects and works properly! Please use only 1 exit and at most 1 trap per maze.`c");
output("`cUse every spot of the maze before generating maze.`c");
$section=$op;
if ($op == "clear"){
	output("`4`bAre you Sure you want to Clear this Maze?`b`n");
	addnav("Yes","runmodule.php?module=mazeedit&op=clear2");
	addnav("No","runmodule.php?module=mazeedit&op=reload");
}
if ($op == "clear2"){
	$umazeedit="";
	set_module_pref("mazeedit",$umazeedit);
	redirect("runmodule.php?module=mazeedit");
}else{
if ($umazeedit == ""){
	for ($i=0;$i<143;$i++){
		$maze[$i]="x";
	}
	$umazeedit=implode($maze,",");
	set_module_pref("mazeedit",$umazeedit);
}
$maze=explode(",",$umazeedit);
$mapkey2="";
$mapkey3="<div align=\"center\"><table border=\"0\" cellspacing=\"0\" width=\"100%\" cellpadding=\"0\"><tr><td width=\"50%\">";
	$mapkey3.="`2`bClick a section to edit it.`b";
	$mapkey="";
	for ($i=0;$i<143;$i++){
	$keymap=ltrim($maze[$i]);
	$mazemap=$keymap;
	$mazemap.="maze.gif";
	if ($section=="$i" and $op <> "reload"){
		$mapkey.="<a href=\"runmodule.php?module=mazeedit&op=$i\"><img src=\"./images/mazeflash.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	}else{
	$mapkey.="<a href=\"runmodule.php?module=mazeedit&op=$i\"><img src=\"./images/$mazemap\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	addnav("","runmodule.php?module=mazeedit&op=$i");
	}
	if ($i==10 or $i==21 or $i==32 or $i==43 or $i==54 or $i==65 or $i==76 or $i==87 or $i==98 or $i==109 or $i==120 or $i==131 or $i==142){
		$mapkey="`n".$mapkey;
		$mapkey2=$mapkey.$mapkey2;
		$mapkey="";
	}
	}
	$mapkey2=$mapkey3.$mapkey2."</td>";
if ($op == "generate"){
	output("Name your Maze!");
	rawoutput("<form action='runmodule.php?module=mazeedit&op=generate2' method='POST'>");
		rawoutput("<p><input type=\"text\" name=\"mazename\" size=\"37\"></p>");
		rawoutput("<p><input type=\"submit\" value=\"Submit\" name=\"B1\"><input type=\"reset\" value=\"Reset\" name=\"B2\"></p>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=mazeedit&op=generate2");
}elseif ($op <> ""){
	if ($op == "generate2"){
		$mazename=$HTTP_POST_VARS['mazename'];
		for ($i=0;$i<143;$i++){
		$mazecode.=$maze[$i];
		if ($i <142) $mazecode.=",";
	}
	output("`nCopy and Paste into abandoned castle php code.`n");
	output("If you do not have access to the code please e-mail the code to an admin.");
	output("`n`ncase 100:`n");
	$author=str_replace($session['user']['title'],"",$session['user']['name']);
	if ($session['user']['ctitle'] <> "") $author=str_replace($session['user']['ctitle'],"",$author);
	output_notl("//author: $author`n");
	output_notl("//title: $mazename`n");
	output_notl("\$maze = array(".$mazecode.");`n");
	output_notl("break;`n`n");
	}else{
	if ($op <> "reload"){
	$mapkey2.="<td width=\"50%\">";
	$mapkey2.="`n`b`2Maze tiles`b`n";
	if ($section == 5 or (($section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131) and ($section > 11 and $section < 132))) $mapkey2.="<a href=\"runmodule.php?module=mazeedit&op=$section&piece=a\"><img src=\"./images/amaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5 and ($section <> 0 and $section <> 10 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section < 131)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=b\"><img src=\"./images/bmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section == 5 or ($section > 10 and ($section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131 and $section <> 132 and $section <> 142))) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=c\"><img src=\"./images/cmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5 and ($section <> 0 and $section <> 10 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131 and $section <> 132 and $section <> 142)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=d\"><img src=\"./images/dmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	$mapkey2.="<br>";
	if ($section == 5 or ($section > 10 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section < 132)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=e\"><img src=\"./images/emaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section == 5 or ($section > 10 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131 and $section < 132)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=f\"><img src=\"./images/fmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section == 5 or ($section > 10 and $section < 132)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=g\"><img src=\"./images/gmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section == 5 or ($section > 10 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 132)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=h\"><img src=\"./images/hmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	$mapkey2.="<br>";
	if ($section == 5 or ($section > 10 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131 and $section <> 142)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=i\"><img src=\"./images/imaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5 and ($section < 132 and $section <> 10 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131)) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=j\"><img src=\"./images/jmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 0 and $section <> 5 and $section < 132 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=k\"><img src=\"./images/kmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5 and $section < 132) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=l\"><img src=\"./images/lmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	$mapkey2.="<br>";
	if ($section > 10) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=m\"><img src=\"./images/mmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 0 and $section <> 5 and $section <> 11 and $section <> 22 and $section <> 33 and $section <> 44 and $section <> 55 and $section <> 66 and $section <> 77 and $section <> 88 and $section <> 99 and $section <> 110 and $section <> 121 and $section <> 132) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=n\"><img src=\"./images/nmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5 and $section <> 10 and $section <> 21 and $section <> 32 and $section <> 43 and $section <> 54 and $section <> 65 and $section <> 76 and $section <> 87 and $section <> 98 and $section <> 109 and $section <> 120 and $section <> 131 and $section <> 142) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=o\"><img src=\"./images/omaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=p\"><img src=\"./images/pmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	$mapkey2.="<br>";
	if ($section <> 5) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=q\"><img src=\"./images/qmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=r\"><img src=\"./images/rmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=s\"><img src=\"./images/smaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>";
	if ($section <> 5) $mapkey2.=" <a href=\"runmodule.php?module=mazeedit&op=$section&piece=z\"><img src=\"./images/zmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"></a>`n";
	$mapkey2.="<br>";
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=a");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=b");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=c");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=d");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=e");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=f");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=g");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=h");
	if ($section == 5 or $section > 10) addnav("","runmodule.php?module=mazeedit&op=$section&piece=i");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=j");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=k");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=l");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=m");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=n");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=o");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=p");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=q");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=r");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=s");
	if ($section <> 5) addnav("","runmodule.php?module=mazeedit&op=$section&piece=z");
	if ($section <> 5) $mapkey2.="<img src=\"./images/zmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"> - Exit`n";
	if ($section <> 5) $mapkey2.="<img src=\"./images/pmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"> - Pit trap`n";
	if ($section <> 5) $mapkey2.="<img src=\"./images/qmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"> - Flood trap`n";
	if ($section <> 5) $mapkey2.="<img src=\"./images/rmaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"> - Crush trap`n";
	if ($section <> 5) $mapkey2.="<img src=\"./images/smaze.gif\" title=\"\" alt=\"\" style=\"width: 20px; height: 20px;\"> - Slice trap`n";
	$mapkey2.="</td>";
	}
	$mapkey2.="</tr></table>";
	
	if ($piece <> ""){
		$maze[$section]=$piece;
		$umazeedit=implode($maze,",");
		set_module_pref("mazeedit",$umazeedit);
		redirect("runmodule.php?module=mazeedit&op=reload");
	}
}
}else{
		$mapkey2.="<td width=\"50%\">";
		$mapkey2.="</td>";
		$mapkey2.="</tr></table>";
}
if ($op <> "clear"){
addnav("Return to the Mundane","village.php");
addnav("Clear Maze","runmodule.php?module=mazeedit&op=clear");
addnav("Generate Maze Code","runmodule.php?module=mazeedit&op=generate");
}
}
output("%s",$mapkey2,true);
//I cannot make you keep this line here but would appreciate it left in.
output("<div style=\"text-align: left;\"><a href=\"http://www.pqcomp.com\" target=\"_blank\">Abandoned Castle by Lonny @ http://www.pqcomp.com</a><br>",true);
page_footer();
}
?>