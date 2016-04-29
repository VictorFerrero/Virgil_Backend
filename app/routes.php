<?php
/*Test Functions*/
$routePrefix = 'Virgil_Backend_Stage/Virgil_Backend/index.php/';

$router->get($routePrefix.'hello/{name}', function($name){
	$str = "Hello, " . $name . "!";
	return json_encode($str);
}, array('before' => 'statsStart', 'after' => 'statsComplete'));


// start of routes for the mobile app
$router->get($routePrefix.'getEntireMuseum/{id}', function($id){
	$museumController = new MuseumController();
	$_REQUEST['id'] = $id;
	return json_encode($museumController->getEntireMuseum());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'getMuseums/{queryString}', function($queryString){
	$museumController = new MuseumController();
	return json_encode($museumController->getMuseums($queryString));
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'getAllMuseums', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->getAllMuseums());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'account/login', function(){
	$accountController = new AccountController();
	return json_encode($accountController->login());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'account/register', function(){
	$accountController = new AccountController();
	return json_encode($accountController->register());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'account/updateAccount', function(){
	$accountController = new AccountController();
	return json_encode($accountController->updateAccount());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'account/deleteAccount', function(){
	// note that this route is not implemented yet. Not sure about how 
	// we are going to handle account deletes
	$accountController = new AccountController();
	return json_encode($accountController->deleteAccount());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'museum/createMuseum', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->createMuseum());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'museum/updateMuseum', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->updateMuseum());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'museum/deleteMuseum', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->deleteMuseum());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'gallery/createGallery', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->createGallery());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'gallery/updateGallery', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->updateGallery());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'gallery/deleteGallery', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->deleteGallery());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'exhibit/createExhibit', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->createExhibit());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'exhibit/updateExhibit', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->updateExhibit());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'exhibit/deleteExhibit', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->deleteExhibit());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'content/createContent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->createContent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'content/updateContent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->updateContent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'content/deleteContent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->deleteContent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'events/createEvent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->createEvent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'events/updateEvent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->updateEvent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'events/deleteEvent', function(){
	$museumController = new MuseumController();
	return json_encode($museumController->deleteEvent());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'events/getEventsForMuseum/{id}', function($id){
	$museumController = new MuseumController();
	return json_encode($museumController->getEventsForMuseum($id));
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

// Beacon routes
$router->post($routePrefix.'beacons/getContentForBeacon', function(){
	$beaconController = new BeaconController();
	return json_encode($beaconController->getContentForBeacon());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'beacons/addContentForBeacon', function(){
	$beaconController = new BeaconController();
	return json_encode($beaconController->addContentForBeacon());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'beacons/updateContentForBeacon', function(){
	$beaconController = new BeaconController();
	return json_encode($beaconController->updateContentForBeacon());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'beacons/deleteContentForBeacon', function(){
	$beaconController = new BeaconController();
	return json_encode($beaconController->deleteContentForBeacon());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

?>
