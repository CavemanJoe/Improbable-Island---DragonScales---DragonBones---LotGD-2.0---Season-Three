<?php
popup_header("Marriage Questions");
$c = translate_inline("Contents");
output_notl("`#<strong><center><a href='petition.php?op=faq'>$c</a></center></strong>`0",true);
addnav("","petition.php?op=faq");
output("`n`c`&`bQuestions about Marriage`b`c`n");
output("`^1. What is Marriage?`n");
output("`@Don't go there... you don't want to know!`n`n");
output("`^2. Where can I get Married?`n");
if (get_module_setting('all')==1&&get_module_setting('oc')==0) {
	output("`@You can just enter a convenient Chapel.");
} elseif (get_module_setting('oc')==1) {
	output("`@Currently, only in the Old Church in %s",get_module_setting('oldchurchplace','oldchurch'));
} else {
	output("`@Currently, only in the Chapel in %s",get_module_setting('chapelloc'));
}
if (get_module_setting("location")==0) $loc="village";
else $loc="garden";
output("in the %s.",$loc);
output("`nHowever, you do need to have been proposed to.");
if (get_module_setting('flirttype')) {
	output("`nFind more information at your local `iLoveshack`i.");
	if (get_module_setting('lall')) {
		output("`nOne in every place..");
	} else {
		output("`nThe closest loveshack to you is in `%%s`@.",get_module_setting('loveloc'));
	}
}
output_notl("`n`n");
output("`^3. Can I get a divorce?`n");
output("`@Yes, no .net marriage is binding `ithank god..`i Ah! Did I type that??!`n`n");
output("`^4. Anything else?`n");
if (get_module_setting("sg")==1) {
	output("`@Same-gender marriages are allowed");
} else {
	output("`@Same-gender marriages are not allowed");
}
output(", and all Wedded couples can be viewed from the list in the Gardens.`n`n");
output("`^5. What about Wedding Rings?`n");
$n = '`5Capelthwaite';
$g = (get_module_setting('gvica')?translate_inline("She"):translate_inline("He"));
if (get_module_setting('oc')==0) $n = get_module_setting("vname");
if (get_module_setting('oc')==1) $g = translate_inline('He');
if (get_module_setting('cost')>0) {
	output("`%%s`@ will sell a cheap one to you, for %s gold.`n`n",$n,get_module_setting('cost'));
} else {
	output("`^%s`@ has a spare one, and %s will send it for you.`n`n",$n,$g);
}
output("`^6. Who's Married?`n");
output("`@View the list in the Gardens!");
popup_footer();
?>