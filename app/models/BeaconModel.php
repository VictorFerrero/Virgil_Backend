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

// we will pass in major and minor values from the beacon to select content.
// major will give us that particular museums unique ID, and minor will be unique id for that beacon
// each record in beacon_content_map will have a contentId
	public function getContentForBeacon() {
		// will be passed in uuid, major, and minor values
		$arrResult = array();
		$success = false;
		try {
			$sql = "SELECT * FROM beacon_content WHERE major=:major AND minor=:minor";
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
		$museumId = $_POST['museumId'];
		$arrResult = array('error' => array());
		$success = false;
		// need to handle image upload
		$arr =  $this->handleUploadedImage($museumId);
		$arrResult['handleImageUpload'] = $arr;
		$pathToContent = $arr['pathToContent'];
		if($arr['success'] == true) {
			try {
				$sql = "INSERT INTO beacon_content VALUES (NULL, :major, :minor,:title, :description, :pathToContent, :beaconContentProfileJSON)";
				$data = array(
					'major' => $_POST['major'],
					'minor' => $_POST['minor'],
					'title' => $_POST['title'],
					'description' => $_POST['description'],
					'pathToContent' => $pathToContent, 
					'beaconContentProfileJSON' => $_POST['beaconContentProfileJSON']
					);
				$STH = $this->dbo->prepare($sql);
				$arrResult['db_result'] = $STH->execute($data);
				$success = true;
			} catch(Exception $e) {
				$arrResult['error'][] = $e->getMessage();
				$success = false;
			}
		}
		else {
			$arrResult['error'][] = "error handling image uploaded";
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	public function updateContentForBeacon() {
		$arrResult = array('error' => array());
		$arr = array(); // tmp variable used for getting response from handleImageUpload
		$success = false;
		$newPathToContent = "";
		$oldPathToContent = "";
		// get the path to the content that is currently in the db, only do that if update contains new image
		if(isset($_FILES['imageToUpload']['name'])) {
			try {
				$sql = "SELECT pathToContent FROM beacon_content WHERE id=:id";
				$STH = $this->dbo->prepare($sql);
				$STH->bindParam(":id", $_POST['id']);
				$STH->execute();
				$fetch = $STH->fetch(PDO::FETCH_ASSOC);
				$oldPathToContent = $fetch['pathToContent'];
				//echo "oldPathToContent:" . $oldPathToContent;
				$success = true;
			} catch(Exception $e) {
				$arrResult['error'][] = $e->getMessage();
				$success = false;
			}
		}
		// we were able to grab the location of the old content
		if ($success == true) {
			// see if there is a file pending upload
			if(isset($_FILES["imageToUpload"]["name"])) {
				// handle the image: store it in proper directory, make directory path
				$arr = $this->handleUploadedImage($_POST['museumId']);
				$arrResult['debug'] = $arr;
				if($arr['success'] == true) {
					$newPathToContent = $arr['pathToContent'];
					$pathToDelete = "/var/www/html/Virgil_Uploads/beacons/" . $oldPathToContent;
					$dir = "/var/www/html/Virgil_Uploads/beacons/" . $_POST['museumId'];
					if(is_dir($dir)) {
						// some content might not have an image associated with it. Lets make
						// sure we dont try to delete something that isnt there
						 unlink($pathToDelete);
					}
				}
				else {
					$success = false;
				}
			}
		}
		// now we proceed with routine update
		 $sql = "UPDATE beacon_content SET ";
		 $data = array();
		 $index = 0;
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
		 if(isset($_POST['title'])) {
			 $sql = $sql . "title=?, ";
			 $data[$index] = $_POST['title'];
			 $index = $index + 1;
		 }
		 if(isset($_POST['description'])) {
			 $sql = $sql . "description=?, ";
			 $data[$index] = $_POST['description'];
			 $index = $index + 1;
		 }
		 if(strcmp($newPathToContent, "") != 0) {
		 // $newPathToContent will get set if a file upload happens above	
			 $sql = $sql . "pathToContent=?, ";
			 $data[$index] = $newPathToContent;
			 $index = $index + 1;
		 } 
		 if(isset($_POST['beaconContentProfileJSON'])) {
			 $sql = $sql . "beaconContentProfileJSON=?, ";
			 $data[$index] = $_POST['beaconContentProfileJSON'];
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
			 $arrResult['error'][] = $e->getMessage();
			 $success = false;
		 }	
		 $arrResult['success'] = true;
		return $arrResult;
	}

	public function deleteContentForBeacon(){
		$arrResult = array('db_result' => array());
		$success = false;
		$data = array('id' => $_POST['id']);
		$basePath = "/var/www/html/Virgil_Uploads/beacons/";
		// first lets delete the content image from directory
		try {
			$sql = "SELECT pathToContent FROM beacon_content WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			unlink($basePath . $fetch['pathToContent']);
			$sql = "DELETE FROM beacon_content WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$arrResult['db_result'][] = $STH->execute($data);
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}


	private function handleUploadedImage($museumId) {
		$target_dir = "/var/www/html/Virgil_Uploads/beacons/" . $museumId . "/";
		$target_file = $target_dir . basename($_FILES["imageToUpload"]["name"]);
		$pathToContent = $museumId . "/" . basename($_FILES["imageToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$arrResult = array('error' => array());
		// if there is no directory for this museum, then create it
		if (!is_dir($target_dir)) {
   			 mkdir($target_dir, 0777, true);
		}
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["imageToUpload"]["tmp_name"]);
		    if($check !== false) {
		        echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } 
		    else {
		        echo "File is not an image.";
		        $arrResult['error'][] = "File is not an image";
		        $uploadOk = 0;
		    }
		}
		// Check if file already exists
		if (file_exists($target_file)) {
		   echo "Sorry, file already exists.";
		   $arrResult['error'][] = "File already exists";
		    $uploadOk = 0;
		}
		// Check file size. handle this client side
		/*
		if ($_FILES["imageToUpload"]["size"] > 500000) {
		  //  echo "Sorry, your file is too large.";
		  $arrResult['error'][] = "the file is too large";
		    $uploadOk = 0;
		}
		*/
		// Allow certain file formats. handle this client side
		/*
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		 //   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		 $arrResult['error'][] = "image format not supported";
		    $uploadOk = 0;
		}
		*/
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		    $arrResult['error'][] = "You file was not uploaded";
		    $success = false;
		// if everything is ok, try to upload file
		} 
		else {
		    if (move_uploaded_file($_FILES["imageToUpload"]["tmp_name"], $target_file)) {
		    	chmod($target_file, 0777);
		        echo "The file ". basename( $_FILES["imageToUpload"]["name"]). " has been uploaded.";
		    	$success = true;
		    } 
		    else {
		        echo "Sorry, there was an error uploading your file.";
		        $arrResult['error'][] = "Sorry, there was an error uploading your file.";
		    	$success = false;
		    }
		}
		$arrResult['pathToContent'] = $pathToContent;
		$arrResult['success'] = $success;
		return $arrResult;
	}
}
?>
