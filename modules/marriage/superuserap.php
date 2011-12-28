<?php
	require_once("modules/allprefseditor.php");
	allprefseditor_search();
	page_header("Allprefs Editor");
	$subop=httpget('subop');
	$id=httpget('userid');
	addnav("Navigation");
	addnav("Return to the Grotto","superuser.php");
	villagenav();
	addnav("Edit user","user.php?op=edit&userid=$id");
	modulehook('allprefnavs');
	$allprefse=unserialize(get_module_pref('allprefs',"marriage",$id));
	if ($allprefse['flirtsfaith']=="") $allprefse['flirtsfaith']= 0;
	if ($allprefse['flirtsToday']=="") $allprefse['flirtsToday']= 0;
	set_module_pref('allprefs',serialize($allprefse),'marriage',$id);
	if ($subop!='edit'){
		$allprefse=unserialize(get_module_pref('allprefs',"marriage",$id));
		$allprefse['inShack']= httppost('inShack');
		$allprefse['received']= httppost('received');
		$allprefse['buyring']= httppost('buyring');
		$allprefse['counsel']= httppost('counsel');
		$allprefse['flirtsfaith']= httppost('flirtsfaith');
		$allprefse['flirtsToday']= httppost('flirtsToday');
		$allprefse['flirtspouse']= httppost('flirtspouse');
		set_module_pref('allprefs',serialize($allprefse),'marriage',$id);
		output("Allprefs Updated`n");
		$subop="edit";
	}
	if ($subop=="edit"){
		require_once("lib/showform.php");
		$form = array(
			"Marriage Expansion,title",
			"inShack"=>"Is this user in the Loveshack?,bool",
			"received"=>"`%If allowed: Which buff is due for having a proposal accepted or a divorce filed?,enum,0,None,1,Proposal Buff,2,Divorce Buff",
			"buyring"=>"`%Has the user bought a Ring?,bool",
			"counsel"=>"`%Can this user get Marriage Counselling?,bool",
			"flirtsfaith"=>"`%Amount of times been unfaithful?,int",
			"flirtsToday"=>"`^Flirts today?,int",
			"flirtspouse"=>"`^Flirted with spouse today?,bool",
		);
		$allprefse=unserialize(get_module_pref('allprefs',"marriage",$id));
		rawoutput("<form action='runmodule.php?module=marriage&op=superuserap&userid=$id' method='POST'>");
		showform($form,$allprefse,true);
		$click = translate_inline("Save");
		rawoutput("<input id='bsave' type='submit' class='button' value='$click'>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=marriage&op=superuserap&userid=$id");
	}
page_footer();
?>