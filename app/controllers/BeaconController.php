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
