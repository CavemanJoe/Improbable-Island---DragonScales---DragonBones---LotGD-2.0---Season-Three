<?php

output("Who would you like to attack?`n`n");
$search = translate_inline("Search");
rawoutput("<form action='runmodule.php?module=scrapbots&op=findopponent2' method='POST'>");
addnav("","runmodule.php?module=scrapbots&op=findopponent2");
rawoutput("<input name='name' id='name'>");
rawoutput("<input type='submit' class='button' value='$search'>");
rawoutput("</form>");
rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");

?>