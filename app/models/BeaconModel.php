<?php
class BeaconModel
{
	private $dbo;
		
	 public function __construct() {
			$db = new DB_Connections();
			$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}
	
	public function createBeacon() {
		$arrResult = array();
		$success = false;
		try {
			$sql = "INSERT INTO beacons VALUES (NULL, :uuid, :major,:minor, :beaconProfileJSON)";
			$data = array(
				'uuid' => $_POST['uuid'],
				'major' => $_POST['major'],
				'minor' => $_POST['minor'],
				'beaconProfileJSON' => $_POST['beaconProfileJSON']
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
	public function updateBeacon() {
		$arrResult = array();
		$success = false;
		 $sql = "UPDATE beacons SET ";
		 $data = array();
		 $index = 0;
		 if(isset($_POST['uuid'])) {
			 $sql = $sql . "uuid=?, ";
			 $data[$index] = $_POST['uuid'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['major'])) {
			 $sql = $sql . "major=?, ";
			 $data[$index] = $_POST['major'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['minor'])) {
			 $sql = $sql . "minor=?, ";
			 $data[$index] = $_POST['minor'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['beaconProfileJSON'])) {
			 $sql = $sql . "beaconProfileJSON=?, ";
			 $data[$index] = $_POST['beaconProfileJSON'];
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

	public function deleteBeacon() {
		// TODO: delete content associated with a beacon
		$arrResult = array('db_result' => array());
		$success = false;
		$data = array('id' => $_POST['id']);
		try {
			$sql = "SELECT contentId FROM beacons WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);

			// delete all the content associated with this beacon
			$museumController = new MuseumController();
			foreach($fetch as $intIndex => $arrAssoc) {
				$_POST['id'] = $fetch[$intIndex]['contentId'];
				$museumController->deleteContent(); // this will deal with removing the images as well
			}
			// delete from the museum table
			$sql = "DELETE FROM beacons WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

// we will pass in major and minor values from the beacon to select content.
// major will give us that particular museums unique ID, and minor will be unique id for that beacon
// each record in beacon_content_map will have a contentId
	public function getContentForBeacon() {
		// will be passed in uuid, major, and minor values
		$arrResult = array();
		$success = false;
		try {
			$sql = "SELECT * FROM content AS c WHERE major=:major AND minor=:minor INNER JOIN beacon_content_map AS s ON ";
			$sql .= "s.contentId = c.id";
			$data = array(
					'major' => $_POST['major'],
					'minor' => $_POST['minor']
			);
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // could have more then 1 content record associated with this beacon
			$arrResult['beaconContent'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function addContentForBeacon() {
		// lets only worry about inserting into the beacon_content_map table here
		// we will make a seperate call to /content/createContent() to actually create the content
		$arrResult = array();
		$success = false;
		try {
			$sql = "INSERT INTO beacon_content_map VALUES (NULL, :contentId, :uuid,:major, :minor, :profileJSON)";
			$data = array(
				'contentId' => $_POST['contentId'],
				'uuid' => $_POST['uuid'],
				'major' => $_POST['major'],
				'minor' => $_POST['minor'],
				'profileJSON' => $_POST['beaconProfileJSON']
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
}
?>
