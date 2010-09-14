<?php

//Creating a new monster
//Store basic monster information in a serialized array.
//Retrieve the monster information and pass it into the class to create an object.
//Use the object in a battle.

class monster {
	var $hitpoints;
	var $resilience;
	function hit($power){
		$this->hitpoints -= $power;
		return $this->hitpoints;
	}
	function set_hitpoints($new_hitpoints) {
		$this->hitpoints = $new_hitpoints;
	}
	function get_hitpoints() {
		return $this->hitpoints;
	}
}

?>