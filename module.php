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

		$cars = $this->getCars($filter);

		if(!empty($carId)) {

			$newCar = $this->getCar($carId);
			$newCar->isNew(true);

			for($i = 0; $i < count($cars); $i++){

				if($cars[$i]->getId() == $newCar->getId()){
	
					unset($cars[$i]);
				}
			}

			array_unshift($cars, $newCar);
		}

		$subjects = $this->getSubjects();

		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 					=> $cars,
				"subjects" 			=> $subjects,
				"filter" 				=> $filter,
				"isAdmin"				=> true
		));
	}




	public function getCars($filter = null) {

		$yesFilter = "SELECT * FROM car WHERE subject_1 LIKE '%$filter%' ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		$noFilter = "SELECT * FROM car ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		$result = empty($filter) ? Database::query($noFilter) : Database::query($yesFilter);

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




	public function showCarForm($carId = null){

		$car = !empty($carId) ? $this->getCar($carId) : new Car();

		$subjects = $this->getSubjects();

		if(!empty($carId) && !$car->isTest()) throw new Exception("CAR_UPDATE_ERROR: You can only update cars that are marked as test");

		$tpl = new Template("car-form");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array("car" => $car, "subjects" => $subjects));
	}




	public function saveCar(){

		$req = $this->getRequest();
		$data = $req->getBody();

		$car = Car::from_array_or_standard_object($data);

		return empty($data->id) ? $this->createCar($car) : $this->updateCar($car);
	}




	public function createCar(Car $car) {

		$result = insert($car);

		return redirect("/car/list/{$car->getId()}");
	}
	
	
	
	
	// For now only allow updates on test reviews.
	public function updateCar(Car $car) {
	
		if(!$car->isTest()) throw new Exception("CAR_UPDATE_ERROR: You can only update cars that are marked as test");
		
		$result = update($car);

		return redirect("/car/list/{$car->getId()}");
	}




	public function deleteCar($id){

		$car = $this->getCar($id);

		if(!$car->isTest()) throw new Exception("CAR_DELETE_ERROR: You can only delete cars that are marked as test");

		$query = "DELETE FROM car WHERE is_test = 1 AND Id = '$id'";

		$db = new Database();

		$result = $db->delete($query);

		return redirect("/car/list");
	}




	public function flagReview(){

		$req = $this->getRequest();
		$body = $req->getBody();

		$table = $body->tableName;
		$id = $body->carId;
		$isFlagged = $body->is_flagged;

		$query = "UPDATE $table SET is_flagged = $isFlagged WHERE Id = '$id'";

		$database = new Database();
		$result = $database->update($query);

		return "success";
	}
	
	
	public function testCarRoute(){

		return "Hello World!";
	}
}


