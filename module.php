<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use Mysql\DbHelper;
use Mysql\QueryBuilder;
use Http\HttpHeaderCollection;

use function Mysql\insert;
use function Mysql\update;
use function Mysql\select;
use function Session\get_current_user;


class CarModule extends Module {

	private $doSummarize;


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
	}

	public function showCars($newCarId = null) {

		$user = get_current_user();

		$conditions = array(
			"op" => "AND",
			"conditions" => array(
				array(
					"fieldname"	=> "subject",
					"op"		=> "LIKE",
					"syntax"	=> "'%s%%'"
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
					"fieldname"	=> "appellate_judge",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "trial_judge",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "court",
					"op"		=> "=",
					"syntax"	=> "'%s'"
				),
				array(
					"fieldname"	=> "importance",
					"op"		=> "=",
					"syntax"	=> "%s"
				),
				array(
					"fieldname"	=> "is_draft",
					"op"		=> "!=",
					"syntax"	=> "%s"
				)
			)
		);


		$params = !empty($_GET) ? $_GET : $_POST;

		if(!$user->isAdmin()) $params["is_draft"] = 1;

		$this->doSummarize = !empty($params["summarize"]);

		$sql = new QueryBuilder("car");

		$sql->setFields(array("*"));

		if(!empty($params)) $sql->setConditions($conditions, $params);

		$orderBy = $this->doSummarize ? "subject, year DESC, month DESC, day DESC" : "year DESC, month DESC, day DESC";
		$sql->setOrderBy($orderBy);

		$query = $sql->compile();

		$cars = select($query);

		if(!is_array($cars)) $cars = array($cars);


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
				"cars"			     => $cars,
				"searchContainer" 	 => $this->getCarSearch($params, $query),
				"messagesContainer"  => $this->getUserFriendlyMessages($params, $cars, $query),
				"user"			     => $user,
				"groupBy"		     => $this->doSummarize ? "subject" : null
			)
		);
	}


	public function getCarSearch($params, $query) {

		$subjects = DbHelper::getDistinctFieldValues("car", "subject");

		

		$years = DbHelper::getDistinctFieldValues("car", "year");

		$appellateJudges = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trialJudges = DbHelper::getDistinctFieldValues("car", "trial_judge");

		$allJudges = array_merge($appellateJudges, $trialJudges);

		$tpl = new Template("car-search");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"subjects" 					 => $subjects,
			"subject"					 => $params["subject"],
			"years"						 => $years,
			"year"						 => $params["year"],
			"allMonths"					 => $this->getMonths(),
			"month"     				 => $this->getStringMonth($params["month"]),
			"allCourts" 				 => $this->getAppellateCourts(),
			"court"     				 => $params["court"],
			"counties"					 => $this->getOregonCounties(),
			"county"					 => $params["circuit"],
			"judges"					 => $allJudges,
			"selectedAppellateJudge"     => $params["appellate_judge"],
			"selectedTrialJudge"         => $params["trial_judge"],
			"importance"				 => $params["importance"],
			"doSummarize"		 		 => $this->doSummarize,
			"selectedImportance"		 => $params["importance"]
		));

	}

	public function getUserFriendlyMessages($params, $cars, $query){

		$tpl = new Template("car-message");
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
		$subject = $params["subject"];
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

		$subjects = DbHelper::getDistinctFieldValues("car", "subject");
		$subjects = array_map(function($subject) { return ucwords($subject); }, $subjects);

		$appellateJudges = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trialJudges = DbHelper::getDistinctFieldValues("car", "trial_judge");

		$allJudges = array_merge($appellateJudges, $trialJudges);

		// var_dump($subjects);exit;   
		$counties = $this->getOregonCounties();

		$tpl = new Template("car-form");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"car" => $car,
			"subjects" => $subjects,
			"counties" => $counties,
			"judges"   => $allJudges,
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

		$query = "DELETE FROM car WHERE Id = '$id'";

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
	


	############################################################################################################################
	################################ EMAIL FUNCTIONS ###########################################################################
	############################################################################################################################

	public function showMailForm() {

		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

		$form = new Template("car-email-form");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultEmail"		=> get_current_user()->getEmail(),
			"defaultSubject"	=> "Appellate Review - COA, $emailDate",
			"defaultPickerDate" => $pickerDate
		];

		return $form->render($params);
	}

	public function newMail() {

		$params = $this->getRequest()->getBody();

		list($startYear, $startMonth, $startDay) = explode("-", $params->startDate);
		list($endYear, $endMonth, $endDay) = explode("-", $params->endDate);

		$query = "SELECT * FROM car WHERE year >= $startYear AND month >= $startMonth AND day >= $startDay AND year <= $endYear AND month <= $endMonth AND day <= $endDay ORDER BY year DESC, month DESC, day DESC";
		$cars = select($query);
		
		$carsTemplate = new Template("car-email-list");
		$carsTemplate->addPath(__DIR__ . "/templates");

		$carsHTML = $carsTemplate->render(["cars" => $cars]);

		$emailTemplate = new Template("car-email");
		$emailTemplate->addPath(__DIR__ . "/templates");

		$templateParams = [
			"car" => $cars[0],
			"carList" => $carsHTML 
		];

	
		$html = $emailTemplate->render($templateParams);

		return $this->doMail($params, $html);

	}

	public function doMail($params, $html){

		$headers = [
			"To" 		   => $params->to,
			"From" 		   => $params->from,
			"Subject" 	   => $params->subject,
			"Content-Type" => "text/html"
		];

		$headers = HttpHeaderCollection::fromArray($headers);

		$message = new MailMessage();
		$message->setBody($html);
		$message->setHeaders($headers);

		return $message;
	}



	public function getStringMonth($numMonth){
		
		$numMonth = strlen($numMonth) == 1 ? "0$numMonth" : $numMonth;

		return $this->getMonths()[$numMonth];
	}






	
	############################################################################################################################
	###################### HARD CODED DATA FUNCTIONS ###########################################################################
	############################################################################################################################


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



	public function getAppellateCourts(){

		return array(
			"" 						 => "All Courts",
			"Oregon Appellate Court" => "Oregon Appellate Court",
			"Oregon Supreme Court"   => "Oregon Supreme Court"
		);
	}
}


