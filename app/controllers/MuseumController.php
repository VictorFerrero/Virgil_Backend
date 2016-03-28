<?php
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
	
	public function getAllMuseums() {
		return ($this->museumModel->getAllMuseums());
	}
	
	
	public function createMuseum() {
		return ($this->museumModel->createMuseum());
	}
	
	public function updateMuseum(){
		return ($this->museumModel->updateMuseum());
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

	public function getEventsForMuseum() {
		return ($this->museumModel->getEventsForMuseum());
	}
}
?>
