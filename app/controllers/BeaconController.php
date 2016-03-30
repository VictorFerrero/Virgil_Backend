<?php
class BeaconController
{
	private $beaconModel;
	
	// id | to | from | message
	public function __construct() {
		$this->beaconModel = new MuseumModel();
	}
	
	public function __destruct() {
		$this->beaconModel = null;
	}

	public function createBeacon() {
		return ($this->beaconModel->createBeacon());
	}

	public function updateBeacon() {
		return ($this->beaconModel->updateBeacon());
	}

	public function deleteBeacon(){
		return ($this->beaconModel->deleteBeacon());
	}

	public function getContentForBeacon() {
		return ($this->beaconModel->getContentForBeacon());
	}	

	public function addContentForBeacon() {

	}
}
?>
