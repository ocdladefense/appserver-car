<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use function Mysql\insert;
use function Mysql\update;
use function Session\get_current_user;


// test comment


class CarModule extends Module {


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
	}

	public function showCars($carId = null) {


		$subject = !empty($_POST["subject"]) && $_POST["subject"] != "Show All" ? $_POST["subject"] : null;
		$year = !empty($_POST["year"]) && $_POST["year"] != "All Years" ? $_POST["year"] : null;

		$query = $this->getQuery($subject, $year);

		$query .= " LIMIT 5";

		$cars = $this->getCars($query);

		$subjects = $this->getDistinctFieldValues("subject_1");

		$years = $this->getDistinctFieldValues("year");

		$user = get_current_user();


		$tpl = new Template("search-list");
		$tpl->addPath(__DIR__ . "/templates");

		$searchForm = $tpl->render(array(
			"subject"	=> $subject,
			"year"		=> $year,
			"count" 	=> count($cars),
			"subjects" 	=> $subjects,
			"years"		=> $years,
			"groupBy"	=> "subject_!",
			"user"		=> $user
		));


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



		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 				=> $cars,
				"searchForm" 		=> $searchForm,
				"user"				=> $user
		));
	}


	public function showCarsByYear($year){

		$query = "SELECT * FROM car";

		if($year != "All%20Years" && !empty($year)) $query .= " WHERE year = $year";

		$query .= " ORDER BY subject_1 ASC";

		$cars = $this->getCars($query);

		$subjects = $this->getDistinctFieldValues("subject_1");

		$years = $this->getDistinctFieldValues("year");

		$user = get_current_user();


		$tpl = new Template("search-summary");
		$tpl->addPath(__DIR__ . "/templates");

		$searchForm = $tpl->render(array(
			"subject"	=> $subject,
			"year"		=> $year,
			"count" 	=> count($cars),
			"subjects" 	=> $subjects,
			"years"		=> $years,
			"user"		=> $user
		));


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



		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 				=> $cars,
				"searchForm" 		=> $searchForm,
				"groupBy"			=> "subject_1",
				"user"				=> $user
		));
	}



	public function getQuery($subject = null, $year = null) {

		$subjectFilter = "SELECT * FROM car WHERE subject_1 LIKE '%$filter%' ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		$query = "SELECT * FROM car";
		$orderBy = " ORDER BY is_flagged DESC, Year DESC, Month DESC, Day DESC";

		if($subject != null || $year != null){

			$query .= " WHERE ";
		}

		$conditions = array();

		if($subject != null){

			$conditions[] = "subject_1 LIKE '%$subject%'";
		}

		if($year != null){

			$conditions[] = "year = $year";
		}

		return $query . implode(" AND ", $conditions) . $orderBy;
	}

	public function getCars($query){

		$result = Database::query($query);

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




	public function getDistinctFieldValues($field) {

		$result = Database::query("SELECT DISTINCT $field FROM car ORDER BY $field");

		$records = $result->getIterator();

		$values = array();

		foreach($records as $record) {

			$values[] = $record[$field];
		}

		return $values;
	}



	/**
	 * Check that this is Rankin or another admin user.
	 *
	 */
	public function showCarForm($carId = null){

		$user = get_current_user();

		
		if(!$user->isAdmin()) {
			throw new \Exception("You don't have access.");
		}
		
		
		$car = !empty($carId) ? $this->getCar($carId) : new Car();

		$subjects = $this->getDistinctFieldValues("subject_1");

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

		$result = Mysql\insert($car);

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

	public function updateCarANumber() {

		$query = "SELECT * FROM CAR WHERE external_link NOT LIKE '%contentdm.oclc.org%'";
		
		$result = Database::query($query);

		$records = $result->getIterator();

		$cars = array();

		foreach($records as $record){

			$cars[] = Car::from_array_or_standard_object($record);
		}

		var_dump($cars);exit;

		
	}
}


