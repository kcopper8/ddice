<?php
/*
http://jsfiddle.net/UrZM8/4/

ddice
[
	[면체:int, 결과:int]
	, [면체:int, 결과:int]
	, [면체:int, 결과:int]
	, [면체:int, 결과:int]
	...
]
	$ddice_db_id = "";
	$ddice_db_password = "";
	$ddice_db_database = "";

	function ddice_get($key) {
		$mysqli = new mysqli("localhost", $ddice_db_id, $ddice_db_password, $ddice_db_database);
		if ($mysqli->connect_errno) {
    		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		$stmt = $mysqli -> prepare("SELECT * FROM `ddice` WHERE `key` = ?");
		$stmt->bind_param('s', $key);
		$stmt->execute();
		$result = $stmt->get_result();
		$ret = $result -> fetch_object();
		$result -> close();
		$stmt -> close();
		$mysqli -> close();
		return $ret;
	}

	function ddice_register($type) {

	}

	function ddice_roll() {

	}

*/
$ddice_db_id = "hunter";
$ddice_db_password = "hunter";
$ddice_db_database = "hunter";
$ddice_conn = mysql_connect('localhost', $ddice_db_id, $ddice_db_password);
mysql_select_db ($ddice_db_database, $ddice_conn);

function ddice_do_select($paramKey) {
	global $ddice_conn;
	$result = mysql_query("SELECT `key`, `type`, `value` FROM `ddice` WHERE `key` = '$paramKey'", $ddice_conn);

	if ( mysql_affected_rows() == 1) {
		return mysql_fetch_object($result);
	}

	else FALSE;
}

function ddice_do_roll($key, $type) {
	global $ddice_conn;
	$typeArr = explode("_", $type);
	$rolledValue = "";
	foreach ($typeArr as $diceType) {
		if ($rolledValue != "") {
			$rolledValue .= "_";
		}
		$rolledValue .= rand(1, $diceType);
	}
	mysql_query("UPDATE `ddice` set `value` = '$rolledValue', `rolled_date` = NOW() WHERE `key` = '$key'", $ddice_conn);
}

function ddice_register($typeArr) {
	$newKey = time() . "_" . rand(0, 1000);
	$rolledDice = ddice_do_select($newKey);
	$generatedType = "";
	for($i = 0; $i< count($typeArr); $i++) {
		if ($i != 0) {
			$generatedType .= "_";
		}
		$generatedType .= $typeArr[$i];
	}

	mysql_query("INSERT INTO `ddice` (`key`, `type`, `reg_date`) VALUES ('$newKey', '$generatedType', NOW())");
	return $newKey;
}


function ddice_roll($key) {
	$ret_dice = ddice_do_select($key);
	if (isset($ret_dice -> value)) {
		echo "value is set";
		return;
	} else {
		ddice_do_roll($key, $ret_dice -> type);
	}
}
function ddice_get($key) {
	$ret_dice = ddice_do_select($key);
	if (isset($ret_dice -> value)) {
		return $ret_dice;
	} else {
		return ddice_select_ddice($key);
	}
}

class DDice {

	public function register() {
		return new DDice();
	}

	public function roll() {
	}

	public function get() {
		
	}
}

var_dump(DDice::register());
?>

<button>굴리기</button>
<div class="ddice_form">
    <div class="ddice_header">
        <h3>다이스 굴리기</h3>
        <a href="#" title="닫기" class="ddice_close_btn">X</a>
    </div>
    <dl>
        <dt>다이스 선택</dt>
        <dd>
            <ul class="ddice_dices">
        <li><button>4</button></li>
        <li><button>6</button></li>
        <li><button>8</button></li>
        <li><button>10</button></li>
        <li><button>12</button></li>
        <li><button>100</button></li>
            </ul>
        </dd>
        <dt>결과</dt>
        <dd>
    <ul class="ddice_dices ddice_output_dices">
        <li><button>10</button></li>
    </ul>
        </dd>
    </dl>
</div>
