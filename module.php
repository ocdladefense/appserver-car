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
	public function showCars($carId = null) {

		$query = $this->getQuery();

		$cars = select($query);

		if(!is_array($cars)) $cars = array($cars);


		// If there is a new car show it at the top of the list.
		if(!empty($carId)) {

			$promote = select("SELECT * FROM car WHERE id = '$carId'");

			$promote->isNew(true);

			for($i = 0; $i < count($cars); $i++){

				if($cars[$i]->getId() == $promote->getId()){
	
					unset($cars[$i]);
				}
			}

			array_unshift($cars, $promote);
		}





		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		// $tpl = new Template("results-widget");

		// $tpl = new Template("search-widget");



		$list = $tpl->render(
			array(
				"cars" => $cars
			)
		);



		/*
		"searchContainer" 	 => $this->getCarSearch($params, $query),
		"messagesContainer"  => $this->getUserFriendlyMessages($params, $cars, $query),
		"user"			     => get_current_user(),
		"groupBy"		     => $this->doSummarize ? "subject" : null
		*/

		$search = "";




		$message = "";

		$tpl = new Template("car-page");
		$tpl->addPath(__DIR__ . "/templates");
		return $tpl->render(array(
			"controller" => $this,
			"results" => $list,
			"searchWidget" => $search,
			"messageWidget" => $message
		));
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

		$this->doSummarize = !empty($params["summarize"]);

		$sql = new QueryBuilder("car");

		$sql->setFields(array("*"));

		if(!empty($params)) $sql->setConditions($conditions, $params);

		$orderBy = $this->doSummarize ? "subject, year DESC, month DESC, day DESC" : "year DESC, month DESC, day DESC";
		$sql->setOrderBy($orderBy);

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


	public function getOptionLists(){

		$subjects = DbHelper::getDistinctFieldValues("car", "subject");
		$subjects = array_map(function($subject) { return ucwords($subject); }, $subjects);

		$appellateJudges = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trialJudges = DbHelper::getDistinctFieldValues("car", "trial_judge");

		$allJudges = array_merge($appellateJudges, $trialJudges);

		$oregon = new Oregon();
		$counties = $oregon->getCounties();

		return [
			"subjects" => $subjects,
			"allJudges" => $allJudges,
			"counties" => $counties
		];
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

		$startDate = new DateTime($params->startDate);
		$endDate = new DateTime($params->endDate);

		// var_dump($params); exit;

		$html = $this->getRecentCarList($params->court, $startDate, $endDate);
		

		return $this->doMail($params->to, $params->subject, "OCDLA Criminal Appellate Review", $html);
	}


	public function getRecentCarList($court = 'Oregon Appellate Court', DateTime $begin = null, DateTime $end = null) {
		$begin = null == $begin ? new DateTime() : $begin;
		
		$beginMysql = $begin->format('Y-m-j');

		if(null == $end) {
			$query = "SELECT * FROM car WHERE decision_date = '{$beginMysql}'";
			$query .= " AND court = '{$court}'";
		} else {
			$endMysql = $end->format('Y-m-j');
	
			$query = "SELECT * FROM car WHERE decision_date >= '{$beginMysql}'";
			$query .= " AND decision_date <= '{$endMysql}'";
			$query .= " AND court = '{$court}'";
		}



		// print $query;exit;
		// ORDER BY year DESC, month DESC, day DESC";
		$cars = select($query);
		
		// var_dump($cars);exit;

		$list = new Template("email-list");
		$list->addPath(__DIR__ . "/templates");

		$listHtml = $list->render(["cars" => $cars]);

		$body = new Template("email-body");
		$body->addPath(__DIR__ . "/templates");

		$params = [
			"year" => $begin->format('Y'),
			"month" => $begin->format('m'),
			"day" => $begin->format('j'),
			"date" => $begin->format('l, M j  Y'),
			"carList" => $listHtml 
		];

	
		return $body->render($params);
	}





	public function doMail($to, $subject, $title, $content, $headers = array()){

		$headers = [
			"From" 		   => "notifications@ocdla.org",
			"Content-Type" => "text/html"
		];

		$headers = HttpHeaderCollection::fromArray($headers);



		$message = new MailMessage($to);
		$message->setSubject($subject);
		$message->setBody($content);
		$message->setHeaders($headers);
		$message->setTitle($title);

		return $message;
	}



	public function testMail() {


		$to = "jbernal.web.dev@gmail.com";//,rankinjohnsonpdx@gmail.com";
		$subject = "Newest Case Review updates";


		$range = new DateTime("2022-1-10");
		$end = new DateTime();
		$content = $this->getRecentCarList('Oregon Appellate Court', $range, $end);
		

		return $this->doMail($to, $subject, "OCDLA Criminal Appellate Review", $content);
	}











}


