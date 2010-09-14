<?php
function marriage_lovedrinks() {
	$z = 2;
	$s = get_module_setting('loveDrinksAdd');
	if (is_module_installed('drinks')&&$s<$z) {
		$sql = array();
		$ladd=array();
		if ($s<1) { // We use 'lessthan' so more drinks can be packaged with this
			$sql[]="INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Love Brew', 1, 25, 5, 0, 0, 0, 20, 0, 5, 15, 0.0, 0, 0, 'Cedrik reaches under the bar, pulling out a purple cupid shaped bottle... as he pours it into a crystalline glass, the glass shines and he puts a pineapple within the liquid... \"Here, have a Love Brew..\" says Cedrik.. and as you try it, you feel uplifted!', '`%Love Brew', 12, 'You remember love..', 'Despair sets in.', '1.1', '.9', '1.5', '0', '', '', '')";
			$ladd[]="Love Brew";
		}
		if ($s<2) { // We use 'lessthan' so more drinks can be packaged with this
			$sql[]="INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Heart Mist', 1, 25, 5, 0, 0, 0, 20, 0, 5, 15, 0.0, 0, 0, 'Cedrik grabs for a rather garish looking bottle on the shelf behind him... as he pours it into a large yellow mug, the porcelain seems to dissolve.. ooh er.. he puts a tomato within the sweet smelling gunk... \"Here, have a Heart Mist..\" says Cedrik.. and as you try it, you see symbols of love!', '`\$Heart Mist', 18, '`%Misty hearts fly around you..', '`#The sky falls...', '1.1', '.9', '1.5', '0', '', '', '')";
			$ladd[]="Heart Misy";
		}
		foreach ($sql as $val) {
			db_query($val);
		}
		foreach ($ladd as $val) {
			$sql = "SELECT * FROM " . db_prefix("drinks") . " WHERE name='$val' ORDER BY costperlevel";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			set_module_objpref('drinks',$row['drinkid'],'loveOnly',1,'marriage');
		}
		set_module_setting('loveDrinksAdd',$z);
		output("`n`c`b`^Marriage Module - Drinks have been added to the Loveshack`0`b`c");
	} elseif (!is_module_active('drinks')) {
		set_module_setting('loveDrinksAdd',0);
	}
}

function marriage_lovedrinksrem() {
	if (is_module_installed('drinks')) {
		$ladd=array();
		$ladd[]="Love Brew";
		$ladd[]="Heart Mist";
		foreach ($ladd as $val) {
			$sql = "DELETE FROM " . db_prefix("drinks") . " WHERE name='$val'";
			db_query($sql);
		}
	}
}
?>