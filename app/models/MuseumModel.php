<?php
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
		 $sql = "UPDATE gallery SET ";
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
			$sql = "INSERT INTO exhibit VALUES (NULL, :galleryId, :museumId,:exhibitName, :exhibitProfileJSON)";
			$data = array(
				'galleryId' => $_POST['galleryId'],
				'museumId' => $_POST['museumId'],
				'exhibitName' => $_POST['exhibitName'],
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
		 if(isset($_POST['exhibitName'])) {
			 $sql = $sql . "exhibitName=?, ";
			 $data[$index] = $_POST['exhibitName'];
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
		$this->handleUploadedImage(10);
		return "createContent funtion not implemented yet";
	}

	public function updateContent() {
		return "updateContent funtion not implemented yet";
	}

	public function deleteContent() {
		return "deleteContent funtion not implemented yet";
	}

	private function handleUploadedImage($museumId) {
		$target_dir = "uploads/images/" . $museumId . "/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		if (!is_dir($target_dir)) {
   			 mkdir($target_dir, 0777, true);
		}
		/*
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["imageToUpload"]["tmp_name"]);
		    if($check !== false) {
		      //  echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } 
		    else {
		     //   echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}
		// Check if file already exists
		if (file_exists($target_file)) {
		   // echo "Sorry, file already exists.";
		    $uploadOk = 0;
		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 500000) {
		  //  echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		 //   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		    $success = false;
		// if everything is ok, try to upload file
		} 
		else {
		    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		      //  echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		    	$success = true;
		    } 
		    else {
		      //  echo "Sorry, there was an error uploading your file.";
		    	$success = false;
		    }
		}
		return $success;
		*/
	}
}

?>
