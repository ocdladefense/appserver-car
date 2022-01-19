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
				),
				array(
					"fieldname"	=> "month",
					"op"		=> "=",
					"syntax"	=> "%s"
				),
				array(
					"fieldname"	=> "day",
					"op"		=> "=",
					"syntax"	=> "%s"
				),
				array(
					"fieldname"	=> "circuit",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "judges",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "court",
					"op"		=> "=",
					"syntax"	=> "'%s'"
				)
			)
		);

		$params = !empty($_GET) ? $_GET : $_POST;

		$sql = new QueryBuilder("car");

		$sql->setFields(array("*"));

		if(!empty($params)) $sql->setConditions($conditions, $params);

		$sql->setOrderBy("Year DESC, Month DESC, Day DESC");

		$query = $sql->compile();

		$cars = select($query);

		if(!is_array($cars)){

			$newArray = array();
			$newArray[] = $cars;
			$cars = $newArray;
		}

		// If there is a new car show it at the top of the list.
		if(!empty($newCarId)) {

			$newCar = select("SELECT * FROM car WHERE id = '$newCarId'");

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
				"searchForm" 	=> $this->getCarSearch($params, $query),
				"userMessages"  => $this->getUserMessagesBox($params, $cars, $query),
				"user"			=> get_current_user(),
				// for now...
				"groupBy"		=> empty($_GET) ? null : "subject_1"
			)
		);
	}


	public function getCarSearch($params, $query) {

		$subjects = DbHelper::getDistinctFieldValues("car", "subject_1");

		$years = DbHelper::getDistinctFieldValues("car", "year");

		$judges = DbHelper::getDistinctFieldValues("car", "judges");

		$tpl = new Template("search-list");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"subjects" 	=> $subjects,
			"subject"	=> $params["subject_1"],
			"years"		=> $years,
			"year"		=> $params["year"],
			"allMonths"	=> $this->getMonths(),
			"month"     => $this->getStringMonth($params["month"]),
			"allCourts" => $this->getAppellateCourts(),
			"court"     => $params["court"],
			"counties"	=> $this->getOregonCounties(),
			"county"	=> $params["circuit"],
			"judges"	=> $judges,
			"judgeName" => $params["judges"],
			"user"		=> get_current_user(),
		));

	}

	public function getUserMessagesBox($params, $cars, $query){

		$tpl = new Template("user-friendly");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"message"      => $this->getUserMessage($params, count($cars)),
			"user"		   => get_current_user(),
			"query"        => $query
		));
	}


	public function getUserMessage($params, $count){

		$year = $params["year"];
		$month = $this->getStringMonth($params["month"]);
		$day = $params["day"];
		$court = $params["court"];
		$subject = $params["subject_1"];
		$county = $params["circuit"];

		$courtMsg = empty($court) ? "" : "in $court";

		$month = $month == "All Months" ? null : $month;

		if(!empty($month)) $dateMsg = empty($year) ? "for the month of $month (All Years)" : "for $month";
		if(!empty($day)) $dateMsg .= ", $day";
		if(!empty($year)) $dateMsg .= empty($month) ? "for $year" : ", $year";

		$msg = "";

		if(!empty($subject)) $msg .= "<h3>$subject</h3>";

		$msg .= "showing " . $count . " case review(s)";
		if(!empty($courtMsg)) $msg .= " $courtMsg";
		if(!empty($dateMsg)) $msg .= " $dateMsg";

		if(!empty($county)) $msg .= "<h4>$count decision(s) made in $county County</h4>";


		return $msg;
	}



	public function showCarForm($carId = null){

		$user = get_current_user();

		
		if(!$user->isAdmin()) throw new \Exception("You don't have access.");
		
		
		$car = !empty($carId) ? select("SELECT * FROM car WHERE id = '$carId'") : new Car();

		$subjects = DbHelper::getDistinctFieldValues("car", "subject_1");
		$counties = $this->getOregonCounties();

		$tpl = new Template("car-form");
		$tpl->addPath(__DIR__ . "/templates");

		$judges = DbHelper::getDistinctFieldValues("car", "judges");

		return $tpl->render(array(
			"car" => $car,
			"subjects" => $subjects,
			"counties" => $counties,
			"judges" => $judges,
			"allCourts"	   => $this->getAppellateCourts()
		));
	}



	public function saveCar(){

		$req = $this->getRequest();
		$record = (array) $req->getBody();

		foreach($record as $key => $value){

			if(empty($value)) unset($record[$key]);
		}

		$car = Car::from_array_or_standard_object($record);

		return empty($record["id"]) ? $this->createCar($car) : $this->updateCar($car);
	}




	public function createCar(Car $car) {

		$result = insert($car);

		return redirect("/car/list/{$car->getId()}");
	}
	
	
	
	
	// For now only allow updates on test reviews.
	public function updateCar(Car $car) {
	
		$result = update($car);

		return redirect("/car/list/{$car->getId()}");
	}




	public function deleteCar($id){

		$car = select("SELECT * FROM car WHERE id = '$id'");

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
	

	public function getOregonCounties(){

		return array(
			"Baker" 		=> "Baker",
			"Benton" 		=> "Benton",
			"Clackamas"		=> "Clackamas",
			"Clatsop" 		=> "Clatsop",
			"Columbia"		=> "Columbia",
			"Coos"			=> "Coos",
			"Crook"			=> "Crook",
			"Curry"			=> "Curry",
			"Deschutes"		=> "Deschutes",
			"Douglas"		=> "Douglas",
			"Gillam"		=> "Gillam",
			"Grant"			=> "Grant",
			"Harney"		=> "Harney",
			"Hood River"	=> "Hood River",
			"Jackson"		=> "Jackson",
			"Jefferson"		=> "Jefferson",
			"Josephine"		=> "Josephine",
			"Klamath"		=> "Klamath",
			"Lake"			=> "Lake",
			"Lane"			=> "Lane",
			"Lincoln"		=> "Lincoln",
			"Linn"			=> "Linn",
			"Malheur"		=> "Malheur",
			"Marion"		=> "Marion",
			"Morrow"		=> "Morrow",
			"Multnomah"		=> "Multnomah",
			"Polk"			=> "Polk",
			"Sherman"		=> "Sherman",
			"Tillamook"		=> "Tillamook",
			"Umatilla"		=> "Umatilla",
			"Union"			=> "Union",
			"Wallowa"		=> "Wallowa",
			"Wasco"			=> "Wasco",
			"Washington"	=> "Washington",
			"Wheeler"		=> "Wheeler",
			"Yamhill"		=> "Yamhill"
		);
	}


	public function getMonths(){

		return array(
			"" 	   => "All Months",
			"01"   => "January",
			"02"   => "February",
			"03"   => "March",
			"04"   => "April",
			"05"   => "May",
			"06"   => "June",
			"07"   => "July",
			"08"   => "August",
			"09"   => "September",
			"10"   => "October",
			"11"   => "November",
			"12"   => "December"
		);
	}


	public function getStringMonth($numMonth){
		
		$numMonth = strlen($numMonth) == 1 ? "0$numMonth" : $numMonth;

		return $this->getMonths()[$numMonth];
	}


	public function getAppellateCourts(){

		return array(
			"" 						 => "All Courts",
			"Oregon Appellate Court" => "Oregon Appellate Court",
			"Oregon Supreme Court"   => "Oregon Supreme Court"
		);
		
	}


		// public function showListInSummaryContext($year = null, $month = null, $day = null, $court = null){

	// 	var_dump($_GET);exit;

	// 	$conditions = "year = $year";

	// 	$court = urldecode($court);

	// 	if(!empty($month)) $conditions .= " AND month = $month";
	// 	if(!empty($day)) $conditions .= " AND day = $day";
	// 	if(!empty($court)) $conditions .= " AND court = '$court'";

	// 	$query = "SELECT * FROM car";

	// 	if($year != "All%20Years") $query .= " WHERE $conditions";

	// 	$query .= " ORDER BY subject_1 ASC";

	// 	//var_dump($query);exit;

	// 	$cars = select($query);

	// 	$subjects = DbHelper::getDistinctFieldValues("car", "subject_1");

	// 	$years = DbHelper::getDistinctFieldValues("car", "year");

	// 	$user = get_current_user();


	// 	$tpl = new Template("search-summary");
	// 	$tpl->addPath(__DIR__ . "/templates");

	// 	if(!empty($cars)) $date = $cars[0]->getDate();

	// 	$searchForm = $tpl->render(array(
	// 		"subject"	   => $subject,
	// 		"year"	       => $year,
	// 		"month"		   => $month,
	// 		"day"		   => $day,
	// 		"date"		   => $date,
	// 		"court"		   => $court,
	// 		"count" 	   => count($cars),
	// 		"subjects" 	   => $subjects,
	// 		"years"		   => $years,
	// 		"user"		   => get_current_user()
	// 	));


	// 	$tpl = new Template("car-list");
	// 	$tpl->addPath(__DIR__ . "/templates");


	// 	return $tpl->render(array(
	// 			"cars" 				=> $cars,
	// 			"searchForm" 		=> $searchForm,
	// 			"groupBy"			=> "subject_1",
	// 			"user"				=> $user
	// 	));
	// }

	// public function isUrlEncoded($url){

	// 	$decoded = urldecode($url);

	// 	return $decoded != $url;
	// }



		// public function testCarRoute(){

	// 	return "Hello World!";
	// }

	// public function updateCarANumber() {

	// 	$query = "SELECT id, a_number, external_link FROM Car WHERE year = 2021";

	// 	$cars = select($query);

	// 	foreach($cars as $car){

	// 		$exLink = $car->external_link;
	// 		$linkParts = explode("/", $exLink);
	// 		$car->a_number = trim($linkParts[count($linkParts) -1], ".pdf");
	// 	}

	// 	$results = update($cars);

	// 	var_dump($results);exit;
	// }

}


