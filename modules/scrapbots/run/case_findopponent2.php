<?php
$string="%";
$name = httppost('name');
for ($x=0;$x<strlen($name);$x++){
	$string .= substr($name,$x,1)."%";
}
$sql = "SELECT login,name,level FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY level,login";
$result = db_query($sql);
if (db_num_rows($result)<=0){
	output("Sorry, couldn't find anyone who matched that search.`n`n");
}elseif(db_num_rows($result)>100){
	output("Well, that could be anyone!  Wanna try that again?`n`n");
	output("Who would you like to attack?`n`n");
	$search = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=scrapbots&op=findopponent2' method='POST'>");
	addnav("","runmodule.php?module=scrapbots&op=findopponent2");
	rawoutput("<input name='name' id='name'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
}else{
	output("These people matched your search:`n");
	output("TODO: Check if opponent has scrapbots, can be attacked etc`n`n");
	$name = translate_inline("Name");
	$lev = translate_inline("Level");
	rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$lev</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=scrapbots&op=findopponent3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
		output_notl("%s", $row['name']);
		rawoutput("</a></td><td>");
		output_notl("%s", $row['level']);
		rawoutput("</td></tr>",true);
		addnav("","runmodule.php?module=scrapbots&op=findopponent3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
	}
	rawoutput("</table><br />",true);
}
?>