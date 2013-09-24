<?=$_GET["callback"] ?>(<?
	require("ddice.db.php");

	function assertIsset($msg, $value) {
		if(!isset($value)) {
			throw new Exception("Assert Failed!! is not set: " . $msg);
		}

	}

	function assertEquals($msg, $expected, $active) {
		if ($expected != $active) {
			throw new Exception("Assert Failed!! '$active' is not same with '$expected': " . $msg);
		}
	}

	$key = "1231421". time() . rand(1, 10);
	$ddice = DDice::get($key);
	assertEquals("key is must set.", $key, $ddice -> key);

	$ddice->register(array(10, 10, 10, 10));
	assertEquals("state set", REGISTERED, $ddice->state);
	assertEquals("dice set", 4, count($ddice -> dices));
	foreach ($ddice->dices as $dice) {
		assertIsset("type must set", $dice -> type);
	}

	$ddice -> roll();
	assertEquals("state set", ROLLED, $ddice->state);
	assertEquals("dice set", 4, count($ddice -> dices));
	foreach ($ddice->dices as $dice) {
		assertIsset("value must set", $dice -> value);
	}

	$ddice = DDice::get($key);
	assertEquals("key is must set.", $key, $ddice -> key);
	assertEquals("state set", ROLLED, $ddice->state);
	assertEquals("dice set", 4, count($ddice -> dices));
	foreach ($ddice->dices as $dice) {
		assertIsset("type must set", $dice -> type);
		assertIsset("value must set", $dice -> value);
	}


	$key_arr = array();
	for($i = 0; $i < 10; $i++) {
		$key = "1231421". time() . rand(1, 10) . $i;
		$ddice = DDice::get($key);

		$ddice->register(array(10, 10, 10, 10));
		if ($i % 3 == 1)  {
			$ddice->roll();
		}

		$key_arr[]= $key;
	}

	$ddice_list = DDice::getList($key_arr);

	echo json_encode($ddice_list);
?>);