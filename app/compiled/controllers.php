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
<?php
class BeaconController
{
	private $beaconModel;
	
	// id | to | from | message
	public function __construct() {
		$this->beaconModel = new BeaconModel();
	}
	
	public function __destruct() {
		$this->beaconModel = null;
	}

	public function getContentForBeacon() {
		return ($this->beaconModel->getContentForBeacon());
	}	

	public function addContentForBeacon() {
		return ($this->beaconModel->addContentForBeacon()); 
	}

	public function updateContentForBeacon() {
		return ($this->beaconModel->updateContentForBeacon());
	}

	public function deleteContentForBeacon(){
		return ($this->beaconModel->deleteContentForBeacon());
	}
}
?>
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
?><?php
class MuseumController
{
	private $museumModel;
	
	// id | to | from | message
	public function __construct() {
		$this->museumModel = new MuseumModel();
	}
	
	public function __destruct() {
		$this->museumModel = null;
	}

// START museum related functions	
	public function getEntireMuseum() {
		$arrValues = array();
		$id = $_REQUEST['id'];
		
		$arrResult = $this->museumModel->getEntireMuseum($id);
		return $arrResult;
	}

	public function getMuseums($strSearchQuery) {
		$arrResult = $this->museumModel->getMuseums($strSearchQuery);
		return $arrResult;
	}
	
	// TODO: might have to make this route different for cms and android app
	public function getAllMuseums() {
		$arr = $this->museumModel->getAllMuseums();
		foreach($arr['museums'] as $intIndex => $arrAssoc) {
			$profileJson = $arrAssoc['museumProfileJSON'];
			$data = json_decode($profileJson, true);
			$arr['museums'][$intIndex]['museumZipcode'] = $data['zipcode'];
			$arr['museums'][$intIndex]['museumCity'] = $data['city'];
			$arr['museums'][$intIndex]['museumState']= $data['state'];

			if(isset($data['mon'])) {
			$splitTime = $this->splitTimes($data['mon']);
			$arr['museums'][$intIndex]['museumMondayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumMondayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['tue']);
			$arr['museums'][$intIndex]['museumTuesdayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumTuesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['wed']);
			$arr['museums'][$intIndex]['museumWednesdayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumWednesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['thur']);
			$arr['museums'][$intIndex]['museumThursdayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumThursdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['fri']);
			$arr['museums'][$intIndex]['museumFridayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumFridayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sat']);
			$arr['museums'][$intIndex]['museumSaturdayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumSaturdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sun']);
			$arr['museums'][$intIndex]['museumSundayHoursOpen'] = $splitTime[0];
			$arr['museums'][$intIndex]['museumSundayHoursClose'] = $splitTime[1];
		}
		}
		return $arr;
	}
	
	private function splitTimes($strTime) {
		$arrResult = array();
		$arrTmp = explode("-", $strTime);
		$arrResult['startTime'] = $arrTmp[0];
		$arrResult['endTime'] = $arrTmp[1];
		return $arrResult;
	}
	
	public function createMuseum() {
		$arr = $this->museumModel->createMuseum();
		$profileJson = $arr['record']['museumProfileJSON'];
		$data = json_decode($profileJson, true);
		$arr['record']['museumZipcode'] = $data['zipcode'];
		$arr['record']['museumCity'] = $data['city'];
		$arr['record']['museumState']= $data['state'];

			$splitTime = $this->splitTimes($data['mon']);
			$arr['record']['museumMondayHoursOpen'] = $splitTime[0];
			$arr['record']['museumMondayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['tue']);
			$arr['record']['museumTuesdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumTuesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['wed']);
			$arr['record']['museumWednesdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumWednesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['thur']);
			$arr['record']['museumThursdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumThursdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['fri']);
			$arr['record']['museumFridayHoursOpen'] = $splitTime[0];
			$arr['record']['museumFridayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sat']);
			$arr['record']['museumSaturdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumSaturdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sun']);
			$arr['record']['museumSundayHoursOpen'] = $splitTime[0];
			$arr['record']['museumSundayHoursClose'] = $splitTime[1];
		return ($arr);
	}
	
	public function updateMuseum(){
		$arr = $this->museumModel->updateMuseum();
		$profileJson = $arr['record']['museumProfileJSON'];
		$data = json_decode($profileJson, true);
		$arr['record']['museumZipcode'] = $data['zipcode'];
		$arr['record']['museumCity'] = $data['city'];
		$arr['record']['museumState']= $data['state'];

		$splitTime = $this->splitTimes($data['mon']);
			$arr['record']['museumMondayHoursOpen'] = $splitTime[0];
			$arr['record']['museumMondayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['tue']);
			$arr['record']['museumTuesdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumTuesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['wed']);
			$arr['record']['museumWednesdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumWednesdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['thur']);
			$arr['record']['museumThursdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumThursdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['fri']);
			$arr['record']['museumFridayHoursOpen'] = $splitTime[0];
			$arr['record']['museumFridayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sat']);
			$arr['record']['museumSaturdayHoursOpen'] = $splitTime[0];
			$arr['record']['museumSaturdayHoursClose'] = $splitTime[1];

			$splitTime = $this->splitTimes($data['sun']);
			$arr['record']['museumSundayHoursOpen'] = $splitTime[0];
			$arr['record']['museumSundayHoursClose'] = $splitTime[1];
		return ($arr);
	}
	
	// must do delete of all galleries and exhibits
	public function deleteMuseum() {
		return ($this->museumModel->deleteMuseum());
	}
	// END of museum related functions

	// START of gallery related functions 
	public function createGallery() {
		return ($this->museumModel->createGallery());
	}

	public function updateGallery() {
		return ($this->museumModel->updateGallery());
	}

	public function deleteGallery() {
		return ($this->museumModel->deleteGallery());
	}
	// END of gallery related functions

	// START of exhibit related functions
	public function createExhibit() {
		return ($this->museumModel->createExhibit());
	}

	public function updateExhibit() {
		return ($this->museumModel->updateExhibit());
	}

	public function deleteExhibit() {
		return ($this->museumModel->deleteExhibit());
	}
	// END of exhibit related functions

	// START of content related functions
	public function createContent() {
		return ($this->museumModel->createContent());
	}

	public function updateContent() {
		return ($this->museumModel->updateContent());
	}

	public function deleteContent(){
		return ($this->museumModel->deleteContent());
	}
	// END of content related functions

	// START of Event related functions
	public function createEvent() {
		return ($this->museumModel->createEvent());
	}

	public function updateEvent() {
		return ($this->museumModel->updateEvent());
	}	

	public function deleteEvent() {
		return ($this->museumModel->deleteEvent());
	}

	public function getEventsForMuseum($id) {
		return ($this->museumModel->getEventsForMuseum($id));
	}
}
?>
<?php
// TODO: login and logout need work

class UserController{
		
	private $userRole; // 0 = fratmember, 1 = chef, 2 = admin
	private $username;
	private $loggedIn;
	private $userModel;
	
	public function __construct() {
		$this->userModel = new UserModel();
	}
	
	public function __destruct() {
		// ensure that the UserModel destructor gets called to properly
		// close the database connection
		$this->userModel = null;
	}
	
	public function isUserInOrg($userId, $orgId) {
		$userId = $_REQUEST['userId'];
		$orgId = $_REQUEST['orgId'];
		$arrResult = $this->userModel->isUserInOrg($userId, $orgId);
		return $arrResult;
	}
	// TODO: cookies
	public function login() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->login($arrValues['username'], $arrValues['password']);
		if($arrResult['login']) {
			$arrUser = $arrResult['userInfo'];
			$this->username = $arrUser['username'];
			$this->userRole = $arrUser['userRole'];
			$this->loggedIn = true;
		}
		else {
			$this->loggedIn = false;
			$this->username = "";
			$this->userRole = -1;
			print_r($arrResult);
		}
		return $arrResult;
	}
	
	// TODO: cookies
	public function logout() {
		$this->loggedIn = false;
		$this->username = "";
		$this->userRole = -1;
		$arrResult = array();
		$arrResult['logout'] = true;
		return $arrResult;
	}
	
	public function getAllUsers(){
		return $this->userModel->getAllUsers();
	}
	
	public function register(){
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['userRole'] = $_REQUEST['userRole'];
		$arrValues['orgId'] = $_REQUEST['orgId'];
		$arrResult = $this->userModel->register($arrValues);
		return $arrResult;
		/*
		if($arrResult['success']) {
			//successfully added user
			return $arrResult;
		}
		else {
			//there was an error
			print_r($arrResult);
		}
		* */
	}
	
	public function deleteUser() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->deleteUser($arrValues['username'], $arrValues['password']);
		return $arrResult;
		/*
		if($arrResult['success']) {
			//successfully added user
			return $arrResult;
		}
		else {
			//there was an error
			print_r($arrResult);
		}
		*/
	}
}

?>
