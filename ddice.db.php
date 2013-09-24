<?
/*
CREATE TABLE IF NOT EXISTS `ddice` (
  `key` varchar(50) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rolled_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 테이블의 덤프 데이터 `ddice`
--

INSERT INTO `ddice` (`key`, `value`, `type`, `reg_date`, `rolled_date`) VALUES
('123', '111', '555', '2013-09-01 01:15:53', '2013-09-02 01:15:53'),
('1378053745_242', '4_3_4_1', '6_6_6_6', '2013-09-02 01:51:47', '2013-09-02 01:51:47');
*/

$ddice_db_id = "kcopper8";
$ddice_db_password = "sulya16sul";
$ddice_db_database = "kcopper8";
$ddice_conn = mysql_connect('localhost', $ddice_db_id, $ddice_db_password);
mysql_select_db ($ddice_db_database, $ddice_conn);


define("UNREGISTERED", "unregistered");
define("REGISTERED", "registered");
define("ROLLED", "rolled");

class Dice {
	public $type;
	public $value;

	public function roll() {
		if ($this -> value) {
			throw new Exception("unsupported operation");
		}

		$this -> value = rand(1, $this -> type);
	}

	public function create($type, $value = NULL) {
		$dice = new Dice();
		$dice -> type = intval($type);
		if ($value) {
			$dice -> value = intval($value);
		}
		return $dice;
	}
}

class DDice {
	public $state = UNREGISTERED;
	public $key;
	public $dices = array();

	public function forceToRoll($type_arr) {
		if ($this -> state == UNREGISTERED) {
			$this -> register($type_arr);
			$this -> roll();
		} else if($this -> state == REGISTERED){
			$this -> roll();
		}
	}

	public function register($type_arr) {
		if ($this -> state != UNREGISTERED) {
			throw new Exception("unsupported operation, state is : " . $this -> state);
		}

		for($i = 0; $i < count($type_arr); $i++) {
			$this -> dices[] = Dice::create($type_arr[$i]);
		}

		$this -> nextState();

		$to_insert_key = $this -> key;
		$generatedType = $this -> generateType();
		mysql_query("INSERT INTO `ddice` (`key`, `type`, `reg_date`) VALUES ('$to_insert_key', '$generatedType', NOW())");
	}

	private function nextState() {
		switch ($this -> state) {
			case UNREGISTERED:
				$this -> state = REGISTERED;
				break;
			case REGISTERED:
				$this -> state = ROLLED;
				break;
			default:
				break;
		}
	}

	private function generateType() {
		$type_arr = array();

		for($i = 0; $i < count($this -> dices); $i++) {
			$dice = $this -> dices[$i];
			$type_arr[] = $dice -> type;
		}

		return implode("_", $type_arr);
	}

	private function generateValue() {
		$value_arr = array();

		for($i = 0; $i < count($this -> dices); $i++) {
			$dice = $this -> dices[$i];
			$value_arr[] = $dice -> value;
		}

		return implode("_", $value_arr);
	}

	public function roll() {
		if ($this -> state != REGISTERED) {
			throw new Exception("unsupported operation, state is : " . $this -> state);
		}

		for($i = 0; $i < count($this -> dices); $i++) {
			$dice = $this -> dices[$i];
			$dice -> roll();
		}

		$this -> nextState();

		$to_update_key = $this -> key;
		$generatedValue = $this -> generateValue();
		mysql_query("UPDATE `ddice` set `value` = '$generatedValue', `rolled_date` = NOW() WHERE `key` = '$to_update_key'");
	}

	private function createInstance($key, $ret_obj = NULL) {
		$ddice = new DDice();
		$ddice -> key = $key;
		$ddice -> state = UNREGISTERED;

		if (!$ret_obj) {
			return $ddice;
		}

		if (isset($ret_obj ->value)) {
			$ddice -> state = ROLLED;
		} else {
			$ddice -> state = REGISTERED;
		}

		$ddice -> dices = DDice::parse($ret_obj -> type, $ret_obj -> value);
		return $ddice;
	}

	public function get($key) {
		$result = mysql_query("SELECT `key`, `type`, `value` FROM `ddice` WHERE `key` = '$key'");

		if ( mysql_affected_rows() != 1) {
			return DDice::createInstance($key);
		}

		$ret_obj = mysql_fetch_object($result);

		return DDice::createInstance($key, $ret_obj);
	}

	public function getList($key_arr) {
		$key_arr_count = count($key_arr);
		if ($key_arr_count < 1) {
			return array();
		}

		for($i = 0; $i < $key_arr_count; $i++) {
			$key_arr[$i] = "'$key_arr[$i]'";
		}

		$keys = implode(",", $key_arr);

		$result = mysql_query("SELECT `key`, `type`, `value` FROM `ddice` WHERE `key` in ($keys)");
		if (!$result) {
			return array();
		}

		$ret_arr = array();
		$size = mysql_affected_rows();
		for ($i = 0; $i < $size; $i++) {
			$ret_obj = mysql_fetch_object($result);
			$ret_arr[] = DDice::createInstance($ret_obj -> key, $ret_obj);
		}

		return $ret_arr;
	}

	private function parse($type, $value) {
		$type_arr = explode("_", $type);
		if (isset($value) && $value) {
			$value_arr = explode("_", $value);
			$size = min(count($type_arr), count($value_arr));
		} else {
			$value_arr = array();
			$size = count($type_arr);
		}

		$dice_arr = array();
		for($i = 0; $i < $size; $i++) {
			$dice_arr[] = Dice::create($type_arr[$i], $value_arr[$i]);
		}

		return $dice_arr;
	}

}
?>