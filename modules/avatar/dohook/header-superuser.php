<?php
	if ($session['user']['superuser'] & SU_AUDIT_MODERATION){
			$sql="SELECT count(u.userid) AS counter FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t ON u.userid=t.userid AND u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1';";
			$result=db_query($sql);
			$num=db_fetch_assoc($result);
			output_notl("`n`n");
			if ($num['counter']>0) output("`\$`b`cCurrently there are `v%s`\$ avatars waiting for validation.`c`b`0`n`n",$num['counter']);
		}

?>