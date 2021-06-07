<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use function Mysql\insert;
use function Mysql\update;





class CarModule extends Module {


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
	}

	public function showCars($carId = null) {

		$filter = !empty($_POST["filter"]) ? $_POST["filter"] : null;

		if($carId == null){

			$cars = $this->getCars();
		} else {

			$cars[] = $this->getCar($carId);
		}

		//$cars = $carId == null ? $this->getCars($filter) : $this->getCar($carId);

		$subjects = $this->getSubjects();

		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 				=>  $cars,
				"subjects" 			=> $subjects,
				"filter" 			=> $filter,
				"carId"				=> $carId,
				"isAdmin"			=> true
		));
	}




	public function getCars($filter = null) {

		$yesFilter = "SELECT * FROM car WHERE subject_1 LIKE '%$filter%' ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		$noFilter = "SELECT * FROM car ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		$result = $filter == null ? Database::query($noFilter) : Database::query($yesFilter);

		$records = $result->getIterator();

		$cars = array();
		foreach($records as $record){

			$cars[] = Car::from_array_or_standard_object($record);
		}

		return $cars;
	}




	public function getCar($id){

		$query = "SELECT * FROM car WHERE Id = '$id'";
		
		$result = Database::query($query);

		$records = $result->getIterator();

		$cars = array();
		foreach($records as $record){

			$cars[] = Car::from_array_or_standard_object($record);
		}

		return $cars[0];
	}


	public function getSubjects() {

		$result = Database::query("SELECT subject_1 FROM car ORDER BY subject_1");

		$records = $result->getIterator();

		$subjects = array();

		foreach($records as $subject) {

			$subject = trim($subject["subject_1"]);

			if(!in_array($subject, $subjects)){

				$subjects[] = $subject;
			}
		}

		return $subjects;
	}

	public function flagReview(){

		$req = $this->getRequest();
		$body = $req->getBody();

		$table = $body->tableName;
		$id = $body->carId;
		$isFlagged = $body->isFlagged;

		$query = "UPDATE $table SET is_flagged = $isFlagged WHERE Id = '$id'";

		$database = new Database();
		$result = $database->update($query);

		return "success";
	}

	public function showCarForm($carId = null){

		$car = $carId != null ? $this->getCar($carId) : new Car();

		$tpl = new Template("car-form");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array("car" => $car));
	}


	public function createCart($data) {

		// $data->is_flagged = 1;
		// $data->is_draft = 1;
		$data->is_test = 1;

		$car = Car::from_array_or_standard_object($data);
	
		return $redirect;
	}
	
	
	/**
	 * For now only allow updates on test reviews.
	 */
	public function updateCart($data) {
	
		// Needs fixing: check that is_test is true before doing the update.
		// boolean fields should not be set statically.
		$data->is_flagged = 1;
		$data->is_draft = 1;
		$data->is_test = 1;
	
		$car = Car::from_array_or_standard_object($data);
	
		return $redirect;
	}




	public function saveCar(){

		$req = $this->getRequest();
		$data = $req->getBody();

		if($car->getId() == null){
			return $this->createCart($car);
		} else {
			return $this->updateCart($car);
		}
		
	}


	// needs to be merged into above.
	public function oldSaveCar() {
			$result = insert($car);
			return $this->showCars($result->getId());
		} else {

			$result = update($car);

			if(!$result->hasError()){

				// nope, return redirect.
				return $this->showCars($car->getId());
				
			} else {

				throw new Exception($result->getResult());
			}
		}
	}
	


	public function deleteCar($id){
		// nope, no deleting, but if we do make sure we're only deleting tests.
		return false;
		$query = "DELETE FROM car WHERE is_test = 1 AND Id = '$id'";

		$db = new Database();

		$result = $db->delete($query);

		// nope, redirect.
		return $this->showCars();
	}
	
	
	
	public function testCarRoute(){

		return "Hello World!";
	}
}


