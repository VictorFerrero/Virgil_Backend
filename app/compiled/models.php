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
		 if(isset($_POST['name'])) {
			 $sql = $sql . "name=?, ";
			 $data[$index] = $_POST['name'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['email'])) {
			 $sql = $sql . "email=?, ";
			 $data[$index] = $_POST['email'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['password'])) {
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
<?php

class DB_Connections
{
		
	 public function __construct() {
		
	 }
		
	public function getNewDBO() {
		$arrReturn = array();
		$success = false;
		$db = null;
		// TODO: accessing db credentials=> connection string, username and password??
		$arrCredentials = DatabaseConnectionStrings::getDBCredentials("local");
		
		$dsn = $arrCredentials['dsn'];
		$user = $arrCredentials['username'];
		$password = $arrCredentials['password'];
		$options = $arrCredentials['options'];
		
		try {
			 $db = new PDO($dsn, $user, $password);
			 $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // make PDO throw exceptions
			 $success = true;
		 } catch(Exception $e) {
			 $success = false;
			 $arrReturn['error'] = $e->getMessage();
		 }
		 $arrReturn['success'] = $success;
		 $arrReturn['DBO'] = $db;
		 return $db;
	}	
}

?>
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
?><?php
class MuseumModel
{
	private $dbo;
		
	 public function __construct() {
			$db = new DB_Connections();
			$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}
	
	// START of musuem related functions
	public function getEntireMuseum($id) {
		$arrResult = array();
		$success = false;
		 try {
		 	// lets get the record that corresponds to this museum
		    $sql = "SELECT * FROM museum WHERE id=:id";		
			$data = array('id' => $id);
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // should only be 1 museum with this id
			$arrResult['museum'] = $fetch;

			// now we need to get the Galleries in this museum
			$sql = "SELECT * FROM gallery WHERE museumId=:museumId";
			$data = array('museumId' => $id);
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // could be any amount of galleries, def wanna use fetchAll
			$arrResult['galleries'] = $fetch;

			// now we need to get all the exhibits that are in this museum
			$sql = "SELECT * FROM exhibit WHERE museumId=:museumId";
			$data = array('museumId' => $id);
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // could be any amount of galleries, def wanna use fetchAll
			$arrResult['exhibits'] = $fetch;

			// and finally we need to get all of the content that is in this museum
			$sql = "SELECT * FROM content WHERE museumId=:museumId";
			$data = array('museumId' => $id);
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // could be any amount of galleries, def wanna use fetchAll
			$arrResult['content'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function getMuseums($strSearchQuery) {
		$arrResult = array();
		$success = false;
		try {
			$sql = "SELECT * FROM museum WHERE MATCH(museumName) AGAINST (" . "'" . $strSearchQuery . "'" . ")";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // we might get multiple museums with similar names
			$arrResult['museums'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
	public function getAllMuseums() {
		$arrResult = array();
		$success = false;
		 try {
		    $sql = "SELECT * FROM museum";		
			$STH = $this->dbo->prepare($sql);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); 
			$arrResult['museums'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function createMuseum() {
		$arrResult = array();
		$success = false;
		try {
			$sql = "INSERT INTO museum VALUES (NULL, :accountId, :museumName,:address, :museumProfileJSON)";
			$data = array(
				'accountId' => $_POST['accountId'],
				'museumName' => $_POST['museumName'],
				'address' => $_POST['address'],
				'museumProfileJSON' => $_POST['museumProfileJSON']
				);
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'] = $STH->execute($data);
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function updateMuseum() {
		$arrResult = array();
		$success = false;
		 $sql = "UPDATE museum SET ";
		 $data = array();
		 $index = 0;
		 if(isset($_POST['accountId'])) {
			 $sql = $sql . "accountId=?, ";
			 $data[$index] = $_POST['accountId'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['museumName'])) {
			 $sql = $sql . "museumName=?, ";
			 $data[$index] = $_POST['museumName'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['address'])) {
			 $sql = $sql . "address=?, ";
			 $data[$index] = $_POST['address'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['museumProfileJSON'])) {
			 $sql = $sql . "museumProfileJSON=?, ";
			 $data[$index] = $_POST['museumProfileJSON'];
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

	public function deleteMuseum() {
		// must delete everything!!!
		$arrResult = array('db_result' => array());
		$success = false;
		$data = array('id' => $_POST['id']);
		try {
			// delete from the museum table
			$sql = "DELETE FROM museum WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// delete all the galleries that were associated with this museum
			$sql = "DELETE FROM gallery WHERE museumId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// delete all the exhibits associated with this museum
			$sql = "DELETE FROM exhibit WHERE museumId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// grab all the file paths to the images that are associated with the content
			// that is in this museum
			$sql = "SELECT pathToContent FROM content WHERE museumId=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$content = $STH->fetchAll(PDO::FETCH_ASSOC);
			// go through the content array
			foreach($content as $intIndex => $arrAssoc) {
				$path = $arrAssoc['pathToContent'];
				// TODO: do something to remove the image from the server
			}
			// delete all the content that was associated with this museum
			$sql = "DELETE FROM content WHERE museumId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// now we should be done deleting this museum
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	// END of museum related functions

	// START of gallery related functions
	public function createGallery() {
		$arrResult = array();
		$success = false;
		try {
			$sql = "INSERT INTO gallery VALUES (NULL, :museumId, :galleryName,:galleryProfileJSON)";
			$data = array(
				'museumId' => $_POST['museumId'],
				'galleryName' => $_POST['galleryName'],
				'galleryProfileJSON' => $_POST['galleryProfileJSON']
				);
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'] = $STH->execute($data);
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function updateGallery() {
		$arrResult = array();
		$success = false;
		 $sql = "UPDATE museum SET ";
		 $data = array();
		 $index = 0;
		 if(isset($_POST['museumId'])) {
			 $sql = $sql . "museumId=?, ";
			 $data[$index] = $_POST['museumId'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['galleryName'])) {
			 $sql = $sql . "galleryName=?, ";
			 $data[$index] = $_POST['galleryName'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['galleryProfileJSON'])) {
			 $sql = $sql . "galleryProfileJSON=?, ";
			 $data[$index] = $_POST['galleryProfileJSON'];
			 $index = $index + 1;
		 }
		 // get rid of the last two characters
		 $sql = substr($sql,0,-2);
		 $sql = $sql . "  WHERE id=?";
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

	public function deleteGallery() {
		$id = $_POST['id'];
		$arrResult = array('db_result' => array());
		$success = false;
		$data = array('id' => $_POST['id']);
		try {
			// delete from the gallery
			$sql = "DELETE FROM gallery WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// delete all the exhibits that were associated with this gallery
			$sql = "DELETE FROM exhibit WHERE galleryId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// grab all the file paths to the images that are associated with the content
			// that is in this museum
			$sql = "SELECT pathToContent FROM content WHERE galleryId=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$content = $STH->fetchAll(PDO::FETCH_ASSOC);
			// go through the content array
			foreach($content as $intIndex => $arrAssoc) {
				$path = $arrAssoc['pathToContent'];
				// TODO: do something to remove the image from the server
			}
			// delete all the content that was associated with this museum
			$sql = "DELETE FROM content WHERE galleryId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);
			// now we should be done deleting this gallery
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	// END of gallery related functions

// START of exhibit related functions
	public function createExhibit() {
		$arrResult = array();
		$success = false;
		try {
			$sql = "INSERT INTO exhibit VALUES (NULL, :galleryId, :museumId,:name, :exhibitProfileJSON)";
			$data = array(
				'galleryId' => $_POST['galleryId'],
				'museumId' => $_POST['museumId'],
				'name' => $_POST['name'],
				'exhibitProfileJSON' => $_POST['exhibitProfileJSON']
				);
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'] = $STH->execute($data);
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function updateExhibit() {
		$arrResult = array();
		$success = false;
		 $sql = "UPDATE exhibit SET ";
		 $data = array();
		 $index = 0;
		 if(isset($_POST['galleryId'])) {
			 $sql = $sql . "galleryId=?, ";
			 $data[$index] = $_POST['galleryId'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['museumId'])) {
			 $sql = $sql . "museumId=?, ";
			 $data[$index] = $_POST['museumId'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['name'])) {
			 $sql = $sql . "name=?, ";
			 $data[$index] = $_POST['name'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['exhibitProfileJSON'])) {
			 $sql = $sql . "exhibitProfileJSON=?, ";
			 $data[$index] = $_POST['exhibitProfileJSON'];
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

	public function deleteExhibit() {
		// must delete everything!!!
		$arrResult = array('db_result' => array());
		$success = false;
		$data = array('id' => $_POST['id']);
		try {
			// delete the exhibit 
			$sql = "DELETE FROM exhibit WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);

			// grab all the file paths to the images that are associated with the content
			// that is in this exhibit
			$sql = "SELECT pathToContent FROM content WHERE exhibitId=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$content = $STH->fetchAll(PDO::FETCH_ASSOC);
			// go through the content array
			foreach($content as $intIndex => $arrAssoc) {
				$path = $arrAssoc['pathToContent'];
				// TODO: do something to remove the image from the server
			}
			// delete all the content that was associated with this exhibit
			$sql = "DELETE FROM content WHERE exhibitId=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);
			// now we should be done deleting this museum
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	// END of exhibit related function

	// START of content related functions
	public function createContent() {
		// TODO: need to mess around with uploading images first.
		// will need to write some test scripts so i get the hang of it
		return "createContent funtion not implemented yet";
	}

	public function updateContent() {
		return "updateContent funtion not implemented yet";
	}

	public function deleteContent() {
		return "deleteContent funtion not implemented yet";
	}
}

?>
<?php
/*
 * Org table database schema: 
 * 
 * id | name | address | city | state | zip | phone | email | phone2 | profileJSON
 * 
 * */
class OrgModel{
		private $dbo;

	public function __construct() {
		$db = new DB_Connections();
		$this->dbo = $db->getNewDBO();
	 }
	 public function __destruct() {
		$this->dbo = null;
	}
	
	
	 /**
		expected input: 
		$arrValues => assoc array containing all fields neccessary 
					to add an org to the database
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function createOrg($arrValues) {
		$arrResult = array();
		$success = false;		
		$name = $arrValues['name'];
		$address = $arrValues['address'];
	    $city = $arrValues['city'];
		$state = $arrValues['state'];
		$zip = $arrValues['zip'];
		$phone = $arrValues['phone'];
		$email = $arrValues['email'];
		$phone2 = $arrValues['phone2'];
		$profileJSON = $arrValues['profileJSON'];
		 try {
			$data = array( 
			'name' => $name, 'address' => $address, 
			'city' => $city, 'state' => $state, 
			'zip' => $zip, 'phone' => $phone, 'email' => $email,
			'phone2' => $phone2, 'profileJSON' => $profileJSON);
			$STH = $this->dbo->prepare("INSERT INTO org VALUES (NULL, :name, :address, 
			:city, :state, :zip, :phone, :email, :phone2, :profileJSON)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	 }
	 
	  /**
		expected input: 
		$arrValues => assoc array containing all fields to be changed.
					if a field is not being changed, it must be set to empty string
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function editOrg($arrValues) {
	$arrResult = array();
	$success = false;
	$id = $arrValues['id'];
	$name = $arrValues['name'];
	$address = $arrValues['address'];
	$city = $arrValues['city'];
	$state = $arrValues['state'];
	$zip = $arrValues['zip'];
	$phone = $arrValues['phone'];
	$email = $arrValues['email'];
	$phone2 = $arrValues['phone2'];
	$profileJSON = $arrValues['profileJSON'];
	 $sql = "UPDATE org SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($name, "") != 0) {
		 $sql = $sql . "name=?, ";
		 $data[$index] = $name;
		 $index = $index + 1;
	 }
	 if(strcmp($address, "") != 0) {
		 $sql = $sql . "address=?, ";
		 $data[$index] = $address;
		 $index = $index + 1;
	 }
	 if(strcmp($city, "") != 0) {
		 $sql = $sql . "city=?, ";
		 $data[$index] = $city;
		 $index = $index + 1;
	 }
	 if(strcmp($state, "") != 0) {
		 $sql = $sql . "state=?, ";
		 $data[$index] = $state;
		 $index = $index + 1;
	 }
	  if(strcmp($zip, "") != 0) {
		 $sql = $sql . "zip=?, ";
		 $data[$index] = $zip;
		 $index = $index + 1;
	 }
	  if(strcmp($phone, "") != 0) {
		 $sql = $sql . "phone=?, ";
		 $data[$index] = $phone;
		 $index = $index + 1;
	 }
	  if(strcmp($email, "") != 0) {
		 $sql = $sql . "email=?, ";
		 $data[$index] = $email;
		 $index = $index + 1;
	 }
	  if(strcmp($phone2, "") != 0) {
		 $sql = $sql . "phone2=?, ";
		 $data[$index] = $phone2;
		 $index = $index + 1;
	 }
	  if(strcmp($profileJSON, "") != 0) {
		 $sql = $sql . "profileJSON=?, ";
		 $data[$index] = $profileJSON;
		 $index = $index + 1;
	 }
	 // get rid of the last two characters
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	$arrResult['sql'] = $sql;
	$arrResult['data'] = $data;
	return $arrResult;
	 }
	 
	 /**
		expected input: 
		$id => the id of the org being deleted
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		'db_result' => result of running the query. 1 for success, 0 for failure
		);
	*/	
	 public function deleteOrg($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM org WHERE id=:id";
		try {
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":id", $id);
			$arrResult['db_result'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	 }
	 
	 /**
		expected input: the orgs id
		output:
		$arrResult = array (
		'data' => assoc array containing this orgs record from db
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function getOrgById($id) {
		 $success = false;
		 $arrResult = array();
		 
		 try {
			$stmt = $this->dbo->prepare("SELECT * FROM org WHERE id=:id");
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$success = true;
			$arrResult['data'] = $row; 
		 } catch(Exception $e) {
			 $success = false;
			 $arrResult['error'] = $e->getMessage();
		 }
		 $arrResult['success'] = $success;
		 return $arrResult;
	 }
}
?>
<?php
	// id | username | password | email | userRole | orgId
	//TODO: get true/false if user_id is in org_id
class UserModel{
			
	private $dbo;
	
	 public function __construct() {
			$db = new DB_Connections();
			$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}

// TODO: success is getting set to false when it should be true
	public function isUserInOrg($userId, $orgId) {
		$success = false;
		$arrResult = array();
		$success = false;
		$returnValue = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE id=:id");
			$STH->bindParam(":id", $userId);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$succcess = true;
			if($fetch[0]['orgId'] == $orgId) {
				$returnValue = true;
			}
			else {
				$returnValue = false;
			}	
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		$arrResult['returnValue'] = $returnValue;
		return $arrResult;
	}
	
	/**
		no input
		return list of users
	*/
	public function getAllUsers(){
		try{
			$STH = $this->dbo->prepare("SELECT * FROM user");
			$STH->execute();
			return $STH->fetchAll();
		}
		catch(Exception $e){
			return array("error"=>$e->getMessage());
		}
	}
	
	/**
		expected input: 
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => array of errors that occurred
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function register($arrValues) {
		// first we check if username already exists
		$arrResult = array();
		$success = false;
		$username = $arrValues['username'];
		$hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		$orgId = $arrValues['orgId'];
		$arrResult['error'] = array();
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
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
			$data = array( 'username' => $username, 'password' => $hashedPassword, 'email' => $email, 'orgId' => $orgId, 'userRole' => $userRole);
			$STH = $this->dbo->prepare("INSERT INTO user VALUES (NULL, :username, :password, :email, :userRole, :orgId)");
			$STH->execute($data);
			$success = true;
			// TODO: now, based on the userRole, insert a new record into: member_info, chef_info, or admin_info
				//use same error checks as with the above insert query
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage();
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		// below is for debug
		$arrResult['username'] = $username;
		$arrResult['hashed_password'] = $hashedPassword;
		$arrResult['email'] = $email;
		$arrResult['userRole'] = $userRole;
		return $arrResult;	
	}
	
	/**
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error' => exception object error message
		'success' => true if user was successfuly removed from db, false otherwise
		);
	*/
	public function deleteUser($username,$password) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(password_verify($password,$fetch['password'])){ //TODO: or if admin is deleting a user
				$STH = $this->dbo->prepare("DELETE FROM user WHERE username=:username");
				$STH->bindParam(":username", $username);
				$STH->execute();	
				$success = true;
			} else {
				$success = false;
				$arrResult['error'] = "not authorized to delete this acct";
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	// id | username | password | email | userRole | orgId
	/**
		expected input: => values not being changed must be set to empty string
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		);
	*/
	public function editUser($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $username = $arrValues['username'];
	 $hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
	 $email = $arrValues['email'];
	 $userRole = $arrValues['userRole'];
	 $orgId = $arrValues['orgId'];
	 $sql = "UPDATE user SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($username, "") != 0) {
		 $sql = $sql . "username=?, ";
		 $data[$index] = $username;
		 $index = $index + 1;
	 }
	 if(strcmp($hashedPassword, "") != 0) {
		 $sql = $sql . "password=?, ";
		 $data[$index] = $hashedPassword;
		 $index = $index + 1;
	 }
	 if(strcmp($email, "") != 0) {
		 $sql = $sql . "email=?, ";
		 $data[$index] = $email;
		 $index = $index + 1;
	 }
	 if(strcmp($userRole, "") != 0) {
		 $sql = $sql . "userRole=?, ";
		 $data[$index] = $userRole;
		 $index = $index + 1;
	 }
	  if(strcmp($orgId, "") != 0) {
		 $sql = $sql . "orgId=?, ";
		 $data[$index] = $orgId;
		 $index = $index + 1;
	 }
	 // get rid of the last two characters
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
	/**
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error_message' => invalid username and password pair
		'error' => exception object for first query attempt
		'userInfo' => the assoc array representing the users record in the db
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function login($username, $password) {
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();
		$arrResult['login'] = false;
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
		//	print_r($fetch);
			if(is_array($fetch)) {
				$hashedPassword = $fetch[0]['password'];
//				echo $hashedPassword;
				if(password_verify($password, $hashedPassword)) {
				// username exists in the database and pw hash compare returned true
				$arrResult['userInfo'] = $fetch[0]; // not sure what to return. just putting this here for now
				$arrResult['login'] = true; // the login had the correct credentials
				// find info specific to this type of user
				switch($fetch[0]['userRole']){
					case 0: //member
						//query user_info table and assign to ['member_info']
						break;
					case 1: //chef
						//query chef_info table and assign to ['chef_info']
						break;
					case 2: //admin
						//query admin_info table and assign to ['admin_info']
						break;
					default: 
						//throw error, somehow userRole isn't a number
						break;
				}
				$success = true;
			}
			else {
					$arrResult['error_message'][] = "invalid password";
					$success = false;
				}
			}
			else {
				// invalid username
				$arrResult['error_message'][] = "invalid username";
				$success = false;
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		if(!$success) {
			$arrResult['success'] = $success;
			return $arrResult;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
		/**
		expected input: => the id of the org to get users for
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		'data' => array containing all users that are in the org
		);
	*/
	public function getUsersByOrgId($orgId) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE orgId=:orgId");
			$STH->bindParam(":orgId", $orgId);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
	    return $arrResult;
	}
}

?>
