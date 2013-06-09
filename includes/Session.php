<?php

class Session{

	private $conn;
	private $sess_dtls;

	function __construct
	(
		$server,
		$db,
		$table,
		$username,
		$password
	)
	{
		$this->sess_dtls['sess_server'] = $server;
		$this->sess_dtls['sess_db'] = $db;
		$this->sess_dtls['sess_username'] = $username;
		$this->sess_dtls['sess_password'] = $password;
		$this->sess_dtls['table'] = $table;

	}

	function __destruct ()
	{
		
	}

	private function clean ($input)
	{
		$output = $this->conn->real_escape_string($input);
		return $output;
	}

	function sess_open ()
	{		
		$this->conn = new mysqli($this->sess_dtls['sess_server'],$this->sess_dtls['sess_username'], $this->sess_dtls['sess_password'], $this->sess_dtls['sess_db']);
	}

	function sess_close()
	{
		if (isset($this->conn)) {
			$this->conn->close();
		}
	}

	function sess_read ($id)
	{
		$qrySelect = "SELECT data FROM " . $this->sess_dtls['table'] . " WHERE id='" . $this->clean($id) ."'";
		$result = $this->conn->query($qrySelect);
		
		if ($row = $result->fetch_object()) {
			$ret = $row->data;
			$qryUpdate = "UPDATE " . $this->sess_dtls['table'] . " SET access='" . date('YmdHis') . "' WHERE id='" . $this->clean($id) . "'";
			$this->conn->query($qryUpdate);
		}
		else {
			$ret = '';
		}
		return $ret;
	}

	function sess_write ($id, $data)
	{
		$qryUpdate = "UPDATE " . $this->sess_dtls['table'] . " SET data='" . $data . "', access='" . date('YmdHis') . "' WHERE id='" . $id . "'";
		$this->conn->query($qryUpdate);

		if ($this->conn->affected_rows < 1) {
			$qryInsert = "INSERT INTO " . $this->sess_dtls['table'] . " (id, data, access) VALUES ('" . $id . "','" . $data . "','" . date('YmdHis') . "')";
			$this->conn->query($qryInsert);
		}

		return true;
	}

	function sess_destroy ($id)
	{
		$qryDelete = "DELETE FROM " . $this->sess_dtls['table'] . " WHERE id='" . $id . "'";
		$this->conn->query($qryDelete);
		return true;
	}

	function sess_gc ($timeout)
	{
		$timestamp = date('YmdHis', time() - $timeout);
		$qryDelete = "DELETE FROM " . $this->sess_dtls['table'] . " WHERE access<'" . $timestamp . "'";
		$this->conn->query($qryDelete);
	}

	public static function setHandler() {
		ini_set('session.save_handler', 'user');

		$session = new Session(SESSIONS_DB_SERVER, SESSIONS_DB, SESSIONS_TABLE, SESSIONS_USER, SESSIONS_PASSWORD);

		session_set_save_handler(array($session, 'sess_open'),
			array($session, 'sess_close'),
			array($session, 'sess_read'),
			array($session, 'sess_write'),
			array($session, 'sess_destroy'),
			array($session, 'sess_gc'));

		$cookie_path = "/";
		$cookie_timeout = APP_COOKIE_TIMEOUT;
		session_set_cookie_params($cookie_timeout, $cookie_path);
	}
}
?>
