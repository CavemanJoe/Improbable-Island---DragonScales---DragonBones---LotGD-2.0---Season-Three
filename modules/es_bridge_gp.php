<?php
function es_bridge_gp_getmoduleinfo()
{
	return array
		(
			'name'=>'Equipment Shop - GP Professions Bridge',
			'version'=>'1.0.1',
			'author'=>'`&W`7hite `&K`7night',
			'category'=>'General',
			'description'=>'Synchronizes Equipment Shop data when armor/weapon is purchased via the default GP Pack store.',
			'download'=>'about:',
			'requires'=>array( 'mysticalshop'=>'3.810|Eth\'s Equipment Shop' )
		);
}

function es_bridge_gp_install()
{
	debug( 'Equipment Shop - GP Professions Bridge: Running installation script.' );

	module_addhook( 'footer-runmodule' );
}

function es_bridge_gp_uninstall()
{
	debug( 'Equipment Shop - GP Professions Bridge: Running removal script.' );
}

function es_bridge_gp_dohook( $hook, $args )
{
	global $session, $baseaccount;

	$item = httpget( 'item' );
	$action = httpget( 'action' );

	if( $session['user']['armor'] != $baseaccount['armor']
			&& ( $action == 'weararmor' || $action == 'buyarmor' )
			&& $session['user']['armor'] == $item )
	{
		$category = 'armor';
		$defense = $session['user']['defense'];
	}
	elseif( $session['user']['weapon'] != $baseaccount['weapon']
			&& ( $action == 'wearweapon' || $action == 'buyweapon' )
			&& $session['user']['weapon'] == $item )
	{
		$category = 'weapon';
		$attack = $session['user']['attack'];
	}
	else
	  $category = false;

	if( $category && get_module_pref( $category, 'mysticalshop' ) )
	{
		$current_id = get_module_pref( $category.'id', 'mysticalshop' );
		debug("Current ID is $current_id");
		require_once( './modules/mysticalshop/lib.php' );
		mysticalshop_destroyitem( $category );
		mysticalshop_resetbuffs( $current_id );
		require_once( './modules/mysticalshop_buffs/stripbuff.php' );
		mysticalshop_buffs_stripbuff();
		if( $category == 'armor' )
		  $session['user']['defense'] = $defense;
		else
		  $session['user']['attack'] = $attack;
		debuglog( 'es_bridge_gp: '.$category.' (ID: '.$current_id.') item removed on action "'.$action.'".' );
	}
	return $args;
}
?>