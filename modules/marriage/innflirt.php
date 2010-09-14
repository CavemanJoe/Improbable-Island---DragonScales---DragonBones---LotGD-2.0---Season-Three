<?php
$iname = getsetting("innname", LOCATION_INN);
page_header($iname);
rawoutput("<span style='color: #9900FF'>");
output_notl("`c`b");
output($iname);
output_notl("`b`c");
if ($session['user']['sex']==SEX_MALE) output("As you start to approach Violet, you remember your marriage.`nViolet turns around to give you an evil stare.`nViolet slaps you, then walks off, as she says, \"%s`^Who're you married to then.%s\"","</span>","</span><span style='color: #9900FF'>",true);
else output("As you start to approach Seth, you remember your marriage.`nSeth walks over to give you the evil eye.`nSeth whispers an evil song to you, then walks off, as he says, \"`^Who're you married to then.\"");
marriage_flirtdec();
addnav("Return");
addnav("I?Return to the Inn","inn.php");
villagenav();
page_footer();
?>