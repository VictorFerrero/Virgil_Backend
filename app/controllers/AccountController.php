<?php
class AccountController{
	
	 private $accountController; 
	
	public function __construct() {
		 $this->accountController = new AccountController();
	 }
	 
	 public function __destruct() {
		 $this->accountController = null;
	 }	 
	 
	 public function login() {
		$email = $_REQUEST['email'];
		$password = $_REQUEST['password'];
		$arrResult = $this->userModel->login($email, $password);
		return $arrResult;
	}
	 
	 public function register() {
	 	return ($this->accountController->register());
	 }
	 
	 
	 public function updateAccount() {
		 return ($this->accountController->updateAccount());
	 }
	 
	 public function deleteAccount() {
		 return ($this->accountController->deleteAccount());
	 }
}
?>
