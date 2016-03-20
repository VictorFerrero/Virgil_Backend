<?php
class LogModel
{
	private $dbo;
		
	 public function __construct() {
		$db = new DB_Connections();
		$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}

	public function loginHistory() {

	}

	public function logBeaconCheckin() {
		
	}
}
?>