<?php
/**
	Rewritten by MarcTheSlayer

	10/05/09 - v20090510
	+ Rewrote to handle multi-dimensional arrays and added code to edit player's pref data that's serialised.
	  (some modules use serialised arrays, but don't have built in editors.)
*/
function serialisededitor_getmoduleinfo()
{
	$info = array(
		"name"=>"Serialised Editor",
		"description"=>"Handy serialised array editor for modules that don't have an 'allprefs' editor built in.",
		"version"=>"20090510",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com`2, rewritten by `@MarcTheSlayer",
		"category"=>"Serialised Editor",
		"download"=>"",
		"settings"=>array(
			"array"=>"The array being edited,viewonly",
			"data"=>"ModuleName|Setting|UserID,viewonly",
		),
	);
	return $info;
}

function serialisededitor_install()
{
	if( is_module_active('serialisededitor') )
	{
		output("`c`b`QUpdating 'serialisededitor' Module.`0`b`c`n");
	}
	else
	{
		output("`c`b`QInstalling 'serialisededitor' Module.`0`b`c`n");

		$sarray = array('weapon'=>'sword','armour'=>'shield','horse'=>array('saddle'=>'yes','saddlebags'=>array('clothes'=>array('shirt'=>'yes','trousers'=>'yes','money'=>array('gold'=>36363,'gems'=>32),'shoes'=>'yes')),'feed'=>'straw'),'rucksack'=>array('food'=>'sandwiches and biscuits.','drink'=>'5 cans of lager.'),'pockets'=>array('lint'=>'fluff','button'=>'why is there a button in my pocket?'));
		set_module_setting('array',serialize($sarray));
	}

	module_addhook('superuser');
	return TRUE;
}

function serialisededitor_uninstall()
{
	output("`c`b`QUn-Installing 'serialisededitor' Module.`0`b`c`n");
	return TRUE;
}

function serialisededitor_dohook($hookname,$args)
{
	global $session;

	if( $session['user']['superuser'] & SU_EDIT_USERS )
	{
		addnav('Editors');
		addnav('Edit Serialised Arrays','runmodule.php?module=serialisededitor&op=find');
	}

	return $args;
}

function serialisededitor_run()
{
	page_header('Editing Serialised Array');

	$op = httpget('op');

	if( $op == 'source' )
	{
		if( httpget('subop') == 'save' )
		{
			$code = httppost('code');
			// Don't serialise the array as it already is.
			set_module_setting('array',$code,'serialisededitor');
			output('`#Array has been serialised and saved.`0`n`n');
		}
		else
		{
			// Don't unserialise, leave as is.
			$code = get_module_setting('array','serialisededitor');
		}

		$size = translate_inline(array('Width','Height'));
		$submit = translate_inline('Submit');

		rawoutput('<script language="JavaScript" type="text/javascript">function increase_row(target, value){target.rows = target.rows + value;}function increase_col(target, value){target.cols = target.cols + value;}</script>');
		rawoutput('<form action="runmodule.php?module=serialisededitor&op=source&subop=save" method="POST">');
		addnav('','runmodule.php?module=serialisededitor&op=source&subop=save');
		rawoutput('<table border="0" cellpadding="0" cellspacing="0">');
		rawoutput('<tr><td><textarea name="code" cols="40" rows="10" id="textarea">'.stripslashes($code).'</textarea></td><td rowspan="2" valign="top"><input type="submit" onClick="increase_row(textarea,1);" value="+" />&nbsp;<input type="submit" onClick="increase_row(textarea,-1);" value="-" /><br /><span class="colLtWhite">'.$size[1].'</span></td></tr>');
		rawoutput('<tr><td colspan="2"><input type="submit" onClick="increase_col(textarea,1);" value="+" />&nbsp;<input type="submit" onClick="increase_col(textarea,-1);" value="-" /><br /><span class="colLtWhite">'.$size[0].'</span></td></tr>');
		rawoutput('<tr><td colspan="2"><input type="submit" class="button" value="'.$submit.'" /></td></tr>');
		rawoutput('</table></form>');
	}
	elseif( $op == 'find' )
	{
		$subop = httpget('subop');

		$name = ( httppost('name') ) ? httppost('name') : '';
		$search = translate_inline('Search by name: ');
		$search2 = translate_inline('Search');

		rawoutput('<form action="runmodule.php?module=serialisededitor&op=find&subop=search" method="POST">'.$search.'<input type="text" size="30" name="name" value="'.$name.'" />&nbsp;<input type="submit" value="'.$search2.'" class="button" /></form><br /><br />');
		addnav('','runmodule.php?module=serialisededitor&op=find&subop=search');

		if( $subop == 'search' )
		{
			$search = "%";
			for( $i=0; $i<strlen($name); $i++ )
			{
				$search.=substr($name,$i,1)."%";
			}
			$sql = "SELECT acctid, name, level, login
					FROM " . db_prefix('accounts') . "
					WHERE (locked = 0 AND name LIKE '$search')
					ORDER BY level DESC";
			$result = db_query($sql);

			if( ($max = db_num_rows($result)) > 0 )
			{
				if( $max > 100 )
				{
					output('`#Listing first 100:`n`n');
					$max = 100;
				}

				$ops = translate_inline('Op');
				$pname = translate_inline('Name');
				$login = translate_inline('Login');
				$acctid = translate_inline('AcctID');
				$level = translate_inline('Level');
				$submit = translate_inline('Submit');
				$i = 0;

				rawoutput('<form action="runmodule.php?module=serialisededitor&op=find&subop=list" method="POST">');
				addnav('','runmodule.php?module=serialisededitor&op=find&subop=list');
				rawoutput('<table border="0" celpadding="0" cellspacing="1" align="center">');
				rawoutput('<tr class="trhead"><td>'.$ops.'</td><td>'.$pname.'</td><td>'.$login.'</td><td>'.$acctid.'</td><td>'.$level.'</td></tr>');
				while( $row = db_fetch_assoc($result) )
				{
					rawoutput('<tr class="'.($i%2?'trdark':'trlight').'"><td align="center"><input type="radio" name="userid" value="'.$row['acctid'].'" /></td><td>');
					output_notl('`&%s', $row['name']);
					rawoutput('</td><td>');
					output_notl('%s', $row['login']);
					rawoutput('</td><td align="center">');
					output_notl('%s', $row['acctid']);
					rawoutput('</td><td align="center">');
					output_notl('`^%s', $row['level']);
					rawoutput('</td></tr>');
					$i++;
					if( $i >= $max ) break;
				}

				$sql = "SELECT modulename
						FROM " . db_prefix('modules') . "
					
						ORDER BY modulename ASC";
				$result = db_query($sql);
				$select = '';
				while( $row = db_fetch_assoc($result) )
				{
					$select .= '<option>'.$row['modulename'].'</option>';
				}
				rawoutput('<tr class="trlight"><td colspan="5">');
				output('`#Please select a module: ');
				rawoutput('<select name="modulename">'.$select.'</select></td></tr>');
				rawoutput('<tr class="trdark"><td align="center" colspan="5"><input type="hidden" name="name" value="'.$name.'" /><input type="submit" class="button" value="'.$submit.'" /></td></tr></table></form><br /><br />');
				
			}
			else
			{
				output('`#Sorry, but there were no matches. Please try again.`0`n');
			}
		}
		elseif( $subop == 'list' )
		{
			$userid = httppost('userid');
			$module = httppost('modulename');

			$sql = "SELECT a.name, b.setting, b.value
					FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
					WHERE b.modulename = '".$module."'
						AND a.acctid = b.userid
						AND a.acctid = '".$userid."'";
			$result = db_query($sql);

			if( db_num_rows($result) > 0 )
			{
				output('`#The data in the boxes should still be serialised, if it is not, then it isn\'t a serialised array.`0`n`n');
				$edit = translate_inline('Edit This Array');
				$i = 0;
				while( $row = db_fetch_assoc($result) )
				{
					$array = unserialize($row['value']);
					if( is_array($array) )
					{
						rawoutput('<form action="runmodule.php?module=serialisededitor&op=find&subop=push" method="POST">');
						addnav('','runmodule.php?module=serialisededitor&op=find&subop=push');
						rawoutput('<textarea name="array" cols="40" rows="6">'.$row['value'].'</textarea><br />');
						rawoutput('<input type="hidden" name="data" value="'.$module.'|'.$row['setting'].'|'.$userid.'" /><input type="submit" class="button" value="'.$edit.'" /></form><br /><br />');
						$i++;
					}
				}

				if( $i == 0 )
				{
					output('`#This module has no serialised arrays. Please try another.`0`n');
				}
			}
			else
			{
				output('`#This user does not have any prefs with this module. Please try another.`0`n');
			}
		}
		elseif( $subop == 'push' )
		{
			$data = httppost('data');
			$array = httppost('array');
			if( !empty($data) && !empty($array) )
			{
				set_module_setting('data',$data,'serialisededitor');
				set_module_setting('array',stripslashes($array),'serialisededitor');
				output('`#The array data has been saved and is ready for editing. Click the Editor link to the side to begin.`0`n');
			}
			else
			{
				output('`$Error: `4No array data could be found. Please try again.`0`n');
			}
		}
		elseif( $subop == 'pop' )
		{
			if( httpget('subop2') == 'return' )
			{
				$array = get_module_setting('array','serialisededitor');
				$data = get_module_setting('data','serialisededitor');
				if( !empty($data) )
				{
					list($modulename, $setting, $userid) = explode('|', $data);
					db_query("UPDATE " . db_prefix('module_userprefs') . " SET value = '".$array."' WHERE modulename = '".$modulename."' AND setting = '".$setting."' AND userid = '".$userid."'");

					output("`#The array data has been saved back to its original location.`0`n`n");
					set_module_setting('data','','serialisededitor');
					set_module_setting('array','','serialisededitor');
				}
				else
				{
					output('`$Error: `4The data was missing, could not put the array back.`0`n');
				}
			}
			else
			{
				output('`#If you have finished editing the array then you can return the data back to its original location.`n`n');
				output('Click the link to the side to put it back.');

				addnav('Put Back');
				addnav('Continue','runmodule.php?module=serialisededitor&op=find&subop=push&subop2=return');
			}
		}
	}
	else
	{
		$sarray = unserialize(get_module_setting('array','serialisededitor'));
		// The unserialised array gets added to a new array with the key '0'.
		$names[] = ( !is_array($sarray) ) ? array('population'=>'you') : $sarray;

		$keyid = ( httpget('key') ) ? httpget('key') : httppost('key');
	
		if( !empty($keyid) )
		{
			$urls = '';
			$parts = '';
			$i = 1;

			$keyids = explode('|', $keyid);
			foreach( $keyids as $key => $value )
			{
				// Take a specific array from the multi-dimensional array and add it back with its own key.
				// Only with a specific branch of the tree.
				$names[] = $names[$i-1][$value];

				// This makes the links for under the form.
				$parts .= ( $i == 1 ) ? $value : '|'.$value;
				$urls .= '[<a href="runmodule.php?module=serialisededitor&key='.$parts.'">'.$value.'</a>]';
				addnav('','runmodule.php?module=serialisededitor&key='.$parts);
				$i++;
			}
			// Reverse the array because we want the last to be first and have the key '0'.
			$names = array_reverse($names);
		}

		if( httpget('subop') == 'save' )
		{
			// These are arrays!
			$keyname = httppost('keyname');
			$keyvalue = httppost('keyvalue');
			$keynameorig = httppost('keynameorig');
			$keyvalueorig = httppost('keyvalueorig');

			// Chars to strip out.
			$find = array('"',"'");

			$messages = '`#';
			foreach( $keyname as $key => $value )
			{
				if( !empty($value) )
				{
					// Strip out quotes from key name.
					$value = str_replace($find, '', $value);

					if( isset($keynameorig[$key]) && $value != $keynameorig[$key] )
					{
						// Key name changed, do this first.
						if( !isset($names[0][$value]) )
						{
							// Easiest way to rename is to create a new key with the same value and delete the old key.
							// This is the reason why renamed keys get put last, they're new. :)
							$names[0][$value] = $names[0][$keynameorig[$key]];
							unset($names[0][$keynameorig[$key]]);
							$messages .= 'The key name `b'.$keynameorig[$key].'`b has been renamed to `b'.$value.'`b.`n';
						}
						else
						{
							$messages .= '`$The key name `b'.$value.'`b already exists in this array branch. Please use another.`#`n';
						}
					}

					if( !empty($keyvalue[$key]) )
					{
						if( $keyvalue[$key] == 'ARRAY' )
						{
							// Add a new array.
							if( !empty($keynameorig[$key]) )
							{
								$messages .= 'Existing key name `b'.$value.'`b has been turned into an array.`n';
							}
							else
							{
								$messages .= 'The key name `b'.$value.'`b has been added and made an array.`n';
							}
							$names[0][$value] = array('populate'=>'me');
						}
						else
						{
							// Only did this part for the messages.
							if( !empty($keyvalueorig[$key]) )
							{
								// Old value exists, check to see if it matches the new.
								if( $keyvalueorig[$key] != $keyvalue[$key] )
								{
									$names[0][$value] = $keyvalue[$key];
									$messages .= 'The value of key `b'.$value.'`b has been changed from `b'.$keyvalueorig[$key].'`b to `b'.stripslashes($keyvalue[$key]).'`b.`n';
								}
							}
							else
							{
								// Again for the messages.
								if( isset($names[0][$value]) && isset($keynameorig[$key]) )
								{
									// Existing key has had it's value changed from nothing to something.
									$messages .= 'The value of key name `b'.$value.'`b has been changed from nothing to `b'.stripslashes($keyvalue[$key]).'`b.`n';
								}
								else
								{
									// New key has been added with a value.
									$messages .= 'The key name `b'.$value.'`b has been added with a value of `b'.stipslashes($keyvalue[$key]).'`b.`n';
								}
								$names[0][$value] = $keyvalue[$key];
							}
						}
					}
					else
					{
						// Array keynames don't have any values so check before making null.
						if( !is_array($names[0][$value]) )
						{
							if( empty($keyvalue[$key]) && !empty($keyvalueorig[$key]) )
							{
								// key had a value, but no it doesn't.
								$messages .= 'The value of key name `b'.$value.'`b has been changed from `b'.$keyvalueorig[$key].'`b to nothing.`n';
							}
							elseif( empty($keynameorig[$key]) && empty($keyvalue[$key]) && empty($keyvalueorig[$key]) )
							{
								// New key has been added, but with no value.
								$messages .= 'The key name `b'.$value.'`b has been created and saved with no value.`n';
							}
							$names[0][$value] = '';
						}
					}
				}
				else
				{	// Delete.
					if( isset($names[0][$keynameorig[$key]]) )
					{
						unset($names[0][$keynameorig[$key]]);
						$messages .= 'The key name `b'.$keynameorig[$key].'`b and its value have been deleted.`n';
					}
				}
			}

			// $names is used further down to build the display so make a copy and alter it instead.
			$pass_over = $names[0];

			if( !empty($keyids) )
			{
				$keyids = array_reverse($keyids);
				$count = count($names);
				for( $i=0; $i<$count; $i++ )
				{
					if( isset($names[1]) )
					{
						$shift = array_shift($names);
						$names[0][$keyids[$i]] = $shift;
					}
				}
			}

			set_module_setting('array',serialize($names[0]),'serialisededitor');
			$message .= 'Array has been serialized and saved.`0';

			$names[0] = $pass_over;
			debug($names[0]);
		}

		output('%s`0`n`n', translate_inline($messages));
	
		output('`n`c`#Do not use numbers to name the keys, use words only.`0`c`n`n');

		$trans = translate_inline(array('Key Name','Value','This value is an array.','Extra Boxes','Submit','Click to Edit','Array Tree Branch Links'));

		rawoutput('<form action="runmodule.php?module=serialisededitor&subop=save" method="POST">');
		addnav('','runmodule.php?module=serialisededitor&subop=save');
		rawoutput('<table border="0" width="450" cellpadding="1" cellspacing="1" align="center">');
		rawoutput('<tr class="trhead"><td>'.$trans[0].'</td><td>'.$trans[1].'</td></tr>');
		rawoutput('<tr class="'.(0%2?'trlight':'trdark').'"><td colspan="2">');
		output('`3To delete any of the following, simply delete the key name before submitting. Renaming a key will force it to the bottom.`0');
		rawoutput('</td></tr>');

		// Build the form for the current array tree branch.
		if( !empty($names[0]) )
		{
			$i = 1;
			foreach( $names[0] as $key => $value )
			{
				rawoutput('<tr class="'.($i%2?'trlight':'trdark').'"><td><input type="text" name="keyname[]" value="'.$key.'" /><input type="hidden" name="keynameorig[]" value="'.$key.'" /></td>');
				if( is_array($value) )
				{
					// Don't over write $keyid as it's used below!
					$keyid2 = ( empty($keyid) ) ? '' : $keyid . '|';
					rawoutput('<td>'.$trans[2].' <a href="runmodule.php?module=serialisededitor&key='.$keyid2.$key.'">'.$trans[5].'</a><input type="hidden" name="keyvalue[]" value="" /><input type="hidden" name="keyvalueorig[]" value="" /></td></tr>');
					addnav('','runmodule.php?module=serialisededitor&key='.$keyid2.$key);
				}
				else
				{
					rawoutput('<td><textarea name="keyvalue[]" col="20" row="5">'.stripslashes($value).'</textarea><input type="hidden" name="keyvalueorig[]" value="'.stripslashes($value).'" /></td></tr>');
				}
				$i++;
			}
		}
		else
		{
			rawoutput('<tr class="trlight"><td><input type="text" name="keyname[]" value="" /></td><td><textarea name="keyvalue[]" col="20" row="5"></textarea></td></tr>');
		}

		rawoutput('<tr class="trhead"><td colspan="2">'.$trans[3].'</td></tr>');
		rawoutput('<tr class="trdark"><td colspan="2">');
		output('`3To add another array, simply type `#`bARRAY`b `3into the box and submit. You can do this with existing values, but there\'s no way to convert back once this is done.`0');
		rawoutput('</td></tr><tr class="trlight"><td><input type="text" name="keyname[]" value="" /></td><td><textarea name="keyvalue[]" col="20" row="5"></textarea></td></tr>');
		rawoutput('<tr class="trdark"><td><input type="text" name="keyname[]" value="" /></td><td><textarea name="keyvalue[]" col="20" row="5"></textarea></td></tr>');
		rawoutput('<tr class="trhead"><td colspan="2">'.$trans[6].'</td></tr>');
		rawoutput('<tr class="trlight"><td colspan="2"><a href="runmodule.php?module=serialisededitor">$array</a>'.$urls.'</td></tr>');
		addnav('','runmodule.php?module=serialisededitor');
		rawoutput('<tr class="trdark"><td align="center" colspan="2"><input type="hidden" name="key" value="'.$keyid.'" /><input type="submit" class="button" value="'.$trans[4].'" /></td></tr></table></form><br /><br />');

		rawoutput('<table border="0" cellpadding="2" cellspacing="1" align="center">');
		$text = display_array($names[0]);
		rawoutput("$text");
		rawoutput('</table>');

	}

	addnav('Options');
	addnav('Array Editor','runmodule.php?module=serialisededitor');
	addnav('Find Array To Edit','runmodule.php?module=serialisededitor&op=find');
	addnav('View Array Source','runmodule.php?module=serialisededitor&op=source');
	addnav('Put Array Back','runmodule.php?module=serialisededitor&op=find&subop=pop');

	addnav('Back');
	addnav('Back to the Grotto','superuser.php');

	page_footer();
}

function display_array($sarray = array(), $count = 0)
{
	$colours = array(1=>'00FFFF','00FF00','FFFF00','FF00FF','FF0000','FF9900','0099FF','00B0B0','00B000','B0B000','B000CC','B00000','994400','006BB3','FFFFFF','B0B0B0','0000FF','AABBEE','9A5BEE','0000B0');

	$count++;
	$out = '';

	$spacer = '';
	for( $i=1; $i<=($count*10); $i++ )
	{
		$spacer .= '&nbsp;';
	}

	if( $count == 1 )
	{
		$out .= '<tr class="trlight"><td style="color: #'.$colours[$count].'">array(' . count($sarray) . ')<br />{</td></tr>'."\r\n";
	}
	else
	{
		$out .= '<br /><span  style="color: #'.$colours[$count].'">'.$spacer.'array(' . count($sarray) . ')<br />'.$spacer.'{</span></td></tr>'."\r\n";
	}

	if( is_array($sarray) )
	{
		foreach( $sarray as $key => $value )
		{
			$out .= '<tr class="trlight"><td style="color: #'.$colours[$count].'">'.$spacer.'\''.$key.'\' = '.(is_array($value)?display_array($value, $count):"'".stripslashes($value)."'</td></tr>"."\r\n");
		}
	}

	$out .= '<tr class="trlight"><td style="color: #'.$colours[$count].'">'.($count==1?'':$spacer).'}</td></tr>'."\r\n";

	return $out;
}
?>