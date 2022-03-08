<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use Mysql\DbHelper;
use Mysql\QueryBuilder;
use Http\HttpHeaderCollection;
use GIS\Political\Countries\US\Oregon;
use Ocdla\Date;


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




	/**
	 * @method showCars
	 * 
	 * Show our CAR page with a list of CAR entries
	 * that resulted from the query.  Default query is to show all CARs,
	 * with the most recent CARs first.  The page includes a search widget, a message for
	 * the user summarizing the results of their query and the 
	 * actual list of CAR entires.
	 * 
	 * @param $carId
	 */
	public function getList($recordId = null) {

		$query = $this->getQuery();

		$records = select($query);

		if(!is_array($records)) $records = array($records);


		// If there is a new car show it at the top of the list.
		if(!empty($carId)) {

			$promote = select("SELECT * FROM car WHERE id = '$recordId'");

			$promote->isNew(true);

			for($i = 0; $i < count($cars); $i++){

				if($records[$i]->getId() == $promote->getId()){
	
					unset($records[$i]);
				}
			}

			array_unshift($records, $promote);
		}


		$tpl = new Template("list");
		$tpl->addPath(__DIR__ . "/templates");


		$list = $tpl->render(
			array(
				"records" => $records
			)
		);


		$tpl = new Template("page");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"records" => $records
		));
	}



	private function recordPreprocess($record) {

		static $index = 0; 

		$isFirstClass = $index == 0 ? "is-first" : "";
		$index++;
				
		$isFlagged = $car->isFlagged() ? "checked" : "";

		$classesArray = array();

		if($car->isNew()) $classesArray[] = "is-new";

		$classes = implode(" ", $classesArray);

		$previousSubject = $subject;
		$subject = trim($car->getSubject1());

		$newSubject = $previousSubject != $subject;
		
		$title = $car->getTitle();
		$court = $car->getCourt();

		$importance = !empty($car->getImportance()) ? $car->getImportance() . "/5" : "unset";
		
	}


	private function getQuery() {


		$conditions = array(
			"op" => "AND",
			"conditions" => array(
				array(
					"fieldname"	=> "subject",
					"op"		=> "LIKE",
					"syntax"	=> "'%s%%'"
				),
				array(
					"fieldname"	=> "decision_date",
					"op"		=> "=",
					"syntax"	=> "%s"
				),
				array(
					"fieldname"	=> "county",
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
				)
			)
		);

		$params = !empty($_GET) ? $_GET : $_POST;

		$summarize = !empty($params["summarize"]);

		$sql = new QueryBuilder("car");

		$sql->setFields(array("*"));

		if(!empty($params)) $sql->setConditions($conditions, $params);

	
		$sql->setOrderBy($summarize ? "subject, decision_date DESC" : "decision_date DESC");

		return $sql->compile();
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
	












}


