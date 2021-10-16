<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use Mysql\DbHelper;
use Mysql\QueryBuilder;

use function Mysql\insert;
use function Mysql\update;
use function Mysql\select;
use function Session\get_current_user;


class CarModule extends Module {


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
	}

	public function showCars($newCarId = null) {

		$conditions = array(
			"op" => "AND",
			"conditions" => array(
				array(
					"fieldname"	=> "subject_1",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "year",
					"op"		=> "=",
					"syntax"	=> "%s"
				)
			)
		);

		$sql = new QueryBuilder("Car");

		$sql->setFields(array("*"));

		if(!empty($_POST)) $sql->setConditions($conditions, $_POST);

		$sql->setOrderBy("is_flagged DESC, Year DESC, Month DESC, Day DESC");

		$query = $sql->compile();

		// This the select functions null if there are no records.
		$cars = select($query);


		// If there is a new car show it at the top of the list.
		if(!empty($newCarId)) {

			$newCar = select("SELECT * FROM Car WHERE id = '$newCarId'")[0];

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

		return $tpl->render(
			array(
				"cars"			=> $cars,
				"searchForm" 	=> $this->getCarSearch($_POST, $cars),
				"user"			=> get_current_user()
			)
		);
	}


	public function getCarSearch($params) {

		$subjects = DbHelper::getDistinctFieldValues("Car", "subject_1");

		$years = DbHelper::getDistinctFieldValues("Car", "year");

		$user = get_current_user();

		$tpl = new Template("search-list");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"subject"	=> $params["subject_1"],
			"year"		=> $params["year"],
			"subjects" 	=> $subjects,
			"years"		=> $years,
			"groupBy"	=> "subject_1",
			"user"		=> get_current_user()
		));

	}


	public function showCarsByYear($year){

		$query = "SELECT * FROM car";

		if($year != "All%20Years" && !empty($year)) $query .= " WHERE year = $year";

		$query .= " ORDER BY subject_1 ASC";

		$cars = select($query);

		$subjects = DbHelper::getDistinctFieldValues("Car", "subject_1");

		$years = DbHelper::getDistinctFieldValues("Car", "year");

		$user = get_current_user();


		$tpl = new Template("search-summary");
		$tpl->addPath(__DIR__ . "/templates");

		$searchForm = $tpl->render(array(
			"subject"	=> $subject,
			"year"		=> $year,
			"count" 	=> count($cars),
			"subjects" 	=> $subjects,
			"years"		=> $years,
			"user"		=> get_current_user()
		));


		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 				=> $cars,
				"searchForm" 		=> $searchForm,
				"groupBy"			=> "subject_1",
				"user"				=> $user
		));
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
		
		
		$car = !empty($carId) ? select("SELECT * FROM Car WHERE id = '$carId'")[0] : new Car();

		$subjects = DbHelper::getDistinctFieldValues("Car", "subject_1");

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
	
		//if(!$car->isTest()) throw new Exception("CAR_UPDATE_ERROR: You can only update cars that are marked as test");
		
		$result = update($car);

		return redirect("/car/list/{$car->getId()}");
	}




	public function deleteCar($id){

		$car = select("SELECT * FROM Car WHERE id = '$id'")[0];

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

		$query = "SELECT id, a_number, external_link FROM Car WHERE year = 2021";

		$cars = select($query);

		foreach($cars as $car){

			$exLink = $car->external_link;
			$linkParts = explode("/", $exLink);
			$car->a_number = trim($linkParts[count($linkParts) -1], ".pdf");
		}

		$results = update($cars);

		var_dump($results);exit;
	}
}


