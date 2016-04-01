<?php
class AccountController{
	
	 private $accountModel; 
	
	public function __construct() {
		 $this->accountModel = new AccountModel();
	 }
	 
	 public function __destruct() {
		 $this->accountModel = null;
	 }	 
	 
	 public function login() {
	 	return ($this->accountModel->login());
	}
	 
	 public function register() {
	 	return ($this->accountModel->register());
	 }
	 
	 
	 public function updateAccount() {
		 return ($this->accountModel->updateAccount());
	 }
	 
	 public function deleteAccount() {
		 return ($this->accountModel->deleteAccount());
	 }
}
?>
