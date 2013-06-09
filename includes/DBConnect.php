<?php

class DBConnect extends MySQLi {
	static $db_instance = NULL;
	public $obj_mysqli;
	public $affected_rows;
	public $num_rows;

	private function __construct () {
		$this->obj_mysqli = new mysqli(MY_SERVER, MY_USER, MY_PASSWORD, MY_DB);

		if (mysqli_connect_errno()) {
			throw new Exception("Could not connect to database: " . mysqli_connect_error());
		}// if
	} // __construct

	public static function getInstance () {
		if (self::$db_instance === NULL) {
			self::$db_instance = new self;
		}
		
		return self::$db_instance;
	}

	public function disconnect() {
		$this->obj_mysqli->close();
	}
}
