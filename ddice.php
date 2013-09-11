<?=$_GET["callback"] ?>(<?
/*
http://jsfiddle.net/UrZM8/9/
*/


	if($_GET["ids"]) {
		$ids = explode(",", $_GET["ids"]);
	} else {
		$ids = array();
	}

	$output = array();
	for($i = 0; $i < count($ids); $i++) {
		$id = $ids[$i];

		$output_type = $i % 3;
		switch($output_type) {
			case 2:
			$output[$id] = array("state" => "rolled", "dices" => array(
				array("type" => 10, "value" => rand(1, 10)),
				array("type" => 10, "value" => rand(1, 10)),
				array("type" => 10, "value" => rand(1, 10)),
				array("type" => 10, "value" => rand(1, 10))
			));
			break;
			case 1:
			$output[$id] = array("state" => "registered", "dices" => array(
				array("type" => 10),
				array("type" => 10),
				array("type" => 10),
				array("type" => 10)
			));
			break;
			case 0:
			default;
				$output[$id] = array("state" => "unregistered");
			break;
		}


	}



	echo json_encode($output);
?>);