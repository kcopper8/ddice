<?=$_GET["callback"] ?>(<?

	function generateKey($ddiceKey, $id) {
		return $ddiceKey . '_' . $id;
	}

	function keyToId($key) {
		$arr =  explode("_", $key);
		return $arr[1];
	}

	require('ddice.db.php');

	if($_GET["action"] == "roll") {
		$id = $_GET["id"];
		$types = $_GET["types"];
		$ddiceKey = $_GET["ddiceKey"];

		$key = generateKey($ddiceKey, $id);
		$ddice = DDice::get($key);

		$type_arr = explode(",", $types);
		$ddice -> forceToRoll($type_arr);

		if ($ddice -> state == ROLLED) {
			echo "{'code' : 0}";
		} else {
			echo "{'code' : 1}";
		}
	} else if ($_GET["action"] == "roll_redirect") {
		$id = $_GET["id"];
		$types = $_GET["types"];
		$ddiceKey = $_GET["ddiceKey"];
		$redirect = $_GET["redirect"];

		$key = generateKey($ddiceKey, $id);
		$ddice = DDice::get($key);

		$type_arr = explode(",", $types);
		$ddice -> forceToRoll($type_arr);

		echo("<script>location.href='$redirect';</script>");

	} else {
		if($_GET["ids"]) {
			$ids = explode(",", $_GET["ids"]);
		} else {
			$ids = array();
		}

		if(!$_GET['ddiceKey']) {
			$ids = array();
		}

		$key_arr = array();
		for($i = 0; $i < count($ids); $i++) {
			$id = $ids[$i];
			$key_arr[] = generateKey($_GET['ddiceKey'], $id);
		}
		$ddice_list = DDice::getList($key_arr);

		if (count($ddice_list) > 0) {
			$output = array();
			for($i = 0; $i < count($ddice_list); $i++) {
				$ddice = $ddice_list[$i];
				$output[keyToId($ddice->key)] = $ddice;
			}

			echo json_encode($output);
		} else {
			echo "{}";
		}
	}
?>);
