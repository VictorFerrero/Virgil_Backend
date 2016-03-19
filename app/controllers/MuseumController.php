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
		return ($this->museumModel->deleteMuseum);
	}
}
?>
