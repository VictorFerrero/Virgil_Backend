<?php
class MenuModel
{
	private $dbo;
		
	 public function __construct() {
		$db = new DB_Connections();
		$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}
	
	public function login($email, $password) {
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();
		$arrResult['login'] = false;
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM account WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				$hashedPassword = $fetch[0]['password'];
				if(password_verify($password, $hashedPassword)) {
				// username exists in the database and pw hash compare returned true
				$arrResult['userInfo'] = $fetch[0]; // not sure what to return. just putting this here for now
				$arrResult['login'] = true; // the login had the correct credentials
				// find info specific to this type of user
				$success = true;
			}
			else {
					$arrResult['error_message'][] = "invalid password";
					$success = false;
				}
			}
			else {
				// invalid email
				$arrResult['error_message'][] = "invalid email";
				$success = false;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function register() {
		// first we check if username already exists
		$arrResult = array();
		$success = false;
		$arrResult['error'] = array();
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM account WHERE email=:email");
			$STH->bindParam(":email", $_POST['email']);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// username exists in the db
				$boolValidUsername = false;
				$arrResult['error'][] = "the username already exists";
			}
			else {
				// username is available
				$boolValidUsername = true;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		if(!$boolValidUsername) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// we have a valid username. So lets add it to the db
		 try {
		 	$hashedPassword =  password_hash($_POST['password'], PASSWORD_BCRYPT);
			$data = array
			( 	  'name' => $_POST['name'],
				  'email' => $_POST['email'], 
				  'password' => $hashedPassword,
				  'type' => $_POST['type'],
				  'accountProfileJSON' => $_POST['accountProfileJSON']
				  );
			$STH = $this->dbo->prepare("INSERT INTO account VALUES (NULL, :name, :email, :password, :type, :accountProfileJSON)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage();
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		return $arrResult;	
	}
	
	public function updateAccount() {
		$arrResult = array();
		$success = false;
		 $sql = "UPDATE account SET ";
		 $data = array();
		 $index = 0;
		 if(isset($_POST['name']) {
			 $sql = $sql . "name=?, ";
			 $data[$index] = $_POST['name'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['email']) {
			 $sql = $sql . "email=?, ";
			 $data[$index] = $_POST['email'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['password']) {
			 $sql = $sql . "password=?, ";
			 $hashedPassword =  password_hash($_POST['password'], PASSWORD_BCRYPT);
			 $data[$index] = $hashedPassword;
			 $index = $index + 1;
		 }
		 if(isset($_POST['type'])) {
			 $sql = $sql . "typee=?, ";
			 $data[$index] = $_POST['type'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['accountProfileJSON'])) {
			 $sql = $sql . "accountProfileJSON=?, ";
			 $data[$index] = $_POST['accountProfileJSON'];
			 $index = $index + 1;
		 }
		 // get rid of the last two characters
		 $sql = substr($sql,0,-2);
		 $sql = $sql . " WHERE id=?";
		 $data[$index] = $_POST['id'];
		try {
			 $STH = $this->dbo->prepare($sql);
			 $arrResult['db_result'] = $STH->execute($data);
			 $success = true;
	     } catch (Exception $e) {
			 $arrResult['error'] = $e->getMessage();
			 $success = false;
		 }	
		 // use these for debugging
	//	$arrResult['sql'] = $sql;
	//	$arrResult['data'] = $data;
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function deleteAccount() {
		// lets not worry about this right now. Have to deal with whether or not we delete
		// all museums associated with the account or do we allow the option to transfer it to
		// a different account

		return "function not implemented";
	}
}
?>
