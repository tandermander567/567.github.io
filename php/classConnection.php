<?php
class Connection {
	private $mysqli;
	
	public function __construct() {
		$this->mysqli = new mysqli("localhost", "Имя пользователя", "Пароль", "База данных");
		$this->mysqli->query("SET lc_time_names = 'ru_RU'");
		$this->mysqli->query("SET NAMES 'utf8'");
	}
	
	public function query($query){
		return $this->mysqli->query($query);
	}
	
	public function __destruct() {
		if ($this->mysqli) $this->mysqli->close();
	}
}
?>