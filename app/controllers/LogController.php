<?php
class LogController
{
		
	private $logController; 
	
	public function __construct() {
		 $this->logController = new AccountController();
	 }
	 
	 public function __destruct() {
		 $this->logController = null;
	 }	 

	 public function loginHistory() {

	 }

	 public function logBeaconCheckin() {

	 }
}
?>