<?php
function badnavlog_getmoduleinfo(){
    $info = array(
		"name"=>"Badnav Log",
		"version"=>"1.0.1",
		"author"=>"Cousjava",
		"category"=>"Administrative",
		"download"=>"",
		"description"=>"Records what happens to a badnav to try to test it.",   
    );
    return $info;
}

function badnavlog_install(){
	global $session;
	require_once("lib/tabledescriptor.php");
	output("`4Creating table for badnavlog...`n");
	$badnavlog = array(
		'entryid'=>array('name'=>'entryid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'date'=>array('name'=>'date', 'type'=>'datetime', 'extra'=>'not null'),
		'user'=>array('name'=>'user', 'type'=>'int(11)', 'extra'=>'not null'),
		'allowednavs'=>array('name'=>'allowednavs', 'type'=>'mediumtext', 'extra'=>'not null'),
		'accountsoutput'=>array('name'=>'accountsoutput', 'type'=>'mediumtext', 'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'entryid'),
		'index-user'=>array('name'=>'user', 'type'=>'index', 'columns'=>'user'),
		);
    synctable(db_prefix('badnavlog'), $badnavlog, true);

	module_addhook("badnav");
    return true;
}

function badnavlog_uninstall(){
	output("`4Un-Installing badnavlog Module.`n");
    $sql = "DROP TABLE ".db_prefix("cityprefs");
    db_query($sql);
    return true;
}

function badnavlog_dohook($hookname,$args){
    global $session;
    if ($hookname!="badnav")
    return $args;
	$userid=$session['user']['acctid'];
	$allowednavs=$session['allowednavs'];
	$sql="SELECT output FROM ".db_prefix("accounts_prefix")." WHERE acctid='$userid'";
	$r=db_query($sql);
	$accountsoutput=db_fetch_assoc($r);
	$sql="INSERT INTO ".db_prefix("badnavlog")." (date,user,allowednavs,accountsoutput) VALUES (NOW(),$userid,'$allowednavs','$accountsoutput)";
	db_query($sql);

	return $args;
}

function badnavlog_run() {
    //global $session;
	
	//page_footer();
}
?>
