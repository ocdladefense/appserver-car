<?php

use \Html\HtmlLink;
use function \Html\createElement as createElement;


define("DOM_SECTION_BREAK","<p>&nbsp;</p>");

define("DOM_COMMA",",");

define("DOM_LINE_BREAK","<br />");




class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = $this->carRoutes();
		$this->files = array("CarCreate.php");
		$this->name = "car";
	}

	private function carRoutes() {
		return array(
			"cars"		=> array(
				"callback" => "carSearchForm",
				"Content-Type" => "text/html"
				//"access" => true
			),
			"car-results" => array(
				"callback" => "getCarResults",
				"Content-Type" => "text/html"
			),
			"car-build-select-list" => array(
				"callback" => "getSelectList",
				"Content-Type" => "application/json"
			),
			"car-load-more" => array(
				"callback" => "loadMore",
				"Content-Type" => "text/html"
			),
			"car-create" => array(
				"callback" => "carCreate",
				"Content-Type" => "text/html"
			),
			"car-submit" => array(
				"callback" => "carSubmit",
				"Content-Type" => "text/html"
			),
			"car-update" => array(
				"callback" => "carUpdate",
				"Content-Type" => "text/html",
				"parameters" => ["carId"]
				//"access" => is_user_profile("staffUser")
			),
			"car-submit-update" => array(
				"callback" => "carSubmitUpdate",
				"Content-Type" => "text/html"
			)
		);
	}



	/**
	 * @route "/cars"
	 *
	 * @description Show a list of CARs together with a search form.
	 *  CAR data is stored in a database.  After loading the data we can build the
	 *  HTML page using the new case-reviews template.
	 * 
	 * Sample parameter with one condition
	 * $params = '[{"field":"summary","op":"LIKE","value":"duii"}]';
	 *
	 * @return String The HTML markup, including the case reviews.
	 */
	
	public function carSearchForm($params = array()) {

		// Will already search in default location,
		//  So let's add a pointer to our module's templates.
		Template::addPath(__DIR__ . "/templates");
		

		$loadLimit = 10;

		// Perform a query for CARs in the database.
		// @todo - should return an iterable list of SObjects.
		//if conditons have been passed in then use them to build the query
		//otherwise use the next line for the query
		if(!empty($params)){
			$builder = new QueryBuilder();
			$builder->setTable("car");
			$builder->setConditions(json_decode($params));
			$sql = $builder->compile();

			$results = MysqlDatabase::query($sql);
		}
		else {
			$results = MysqlDatabase::query("SELECT * FROM car ORDER BY full_date DESC LIMIT " . $loadLimit);
		}
		
		// Templates to generate our HTML.
		// $form = $this->getSearchForm();
		$template = Template::loadTemplate("webconsole");

		//Commented out code below moved to it's own function
		/*
		//Number of words to display in the teaser
		$teaserWordLength = 40;
		//Minimun number of characters
		$teaserCutoff = 350;
		$useTeasers = true;
		
		$cases = [];



		foreach($results as $result){

			$case = $result;

			$case["month"] = substr($case["month"], 0, 3);
			$case["month"] .= ".";

			$summaryArray =  explode(" " , $case["summary"]);
			$case['useTeaser'] = $useTeasers === true && strlen($case["summary"]) > $teaserCutoff;

			$case['teaser'] = implode(" ", array_slice($summaryArray, 0, $teaserWordLength));
			$case['readMore'] = implode(" ", array_slice($summaryArray, $teaserWordLength));

			$cases[] = $case;
		} 
		//iterable might be exhausted, may need to rewind here
		*/
		$config = array(
			'teaserWordLength' => 40, 'teaserCutoff' => 350, 'useTeasers' => true
		);
		$cases = $this->formatResults($results, $config); //Call to the function containing the code that was commented out

		$subjects = $this->getListOptions("subject_1");
		$defaultSubject = new stdClass();
		$defaultSubject->name = "All Subjects";
		$defaultSubject->value = "";

		$subjectSettings = new stdClass();
		$subjectSettings->field = "subject_1";
		$subjectSettings->options = $subjects;

		$subjectJson = empty($subjects) ? json_encode(array($defaultSubject)) : json_encode($subjectSettings);

		//$subjectJson = "";

		$config12 = array(
			"numOfMonths" => 12, "inclusive" => false
		);

		$config6 = array(
			"numOfMonths" => 6, "inclusive" => false
		);

		$dateRanges = array(
			["--ALL-- (Select Date Range)", "ALL"],
			["Last Year", $this->calculateDays($config12)], 
			["Last 6 Months", $this->calculateDays($config6)], 
			["Last 30 Days", 30],
			["----------------", "space"], 
			["This Year", $this->thisYear()], 
			["This Month", $this->thisMonth()]
		);

		$dateSettings = new stdClass();
		$dateSettings->field = "datediff(curdate(), full_date)";
		$dateSettings->op = "<";

		$parsedDates = array();
		foreach($dateRanges as $dateRange) {
			$option = new stdClass();
			$option->name = $dateRange[0];
			$option->value = $dateRange[1];
			$parsedDates[] = $option;
		}

		$dateSettings->options = $parsedDates;

		$dateRangesJson = json_encode($dateSettings);

		//Nested arrays describe the field to be ordered by and if it should order by desc
		$sorts = array(
			["Newest to Oldest ", ["full_date", true]],
			["Oldest to Newest", ["full_date", false]],
			["Title Alphabetically", ["title", false]]
		);

		$parsedSorts = array();
		foreach($sorts as $sort) {
			$option = new stdClass();
			$option->name = $sort[0];
			$option->value = $sort[1][0];
			$option->desc = $sort[1][1];
			$parsedSorts[] = $option;
		}

		$sortsJson = json_encode($parsedSorts);

		$searches = array("summary", "title");

		$parsedSearches = array();
		foreach($searches as $search) {
			$option = new stdClass();
			$option->name = $search;
			$option->value = $search;
			$parsedSearches[] = $option;
		}

		$searchesJson = json_encode($parsedSearches);

		$cars = Template::renderTemplate("case-reviews",
			array(
				'cases' 				=> $cases, 
				'subjectJson' 			=> $subjectJson,
				'dateRangesJson'		=> $dateRangesJson,
				'searchesJson'			=> $searchesJson,
				'sortsJson'				=> $sortsJson,
				'loadLimit'				=> $loadLimit,
				'loadOffset'			=> 0
			)
		);


		// ... and custom styles.
		$css = array(
			"active" => true,
			"href" => "/modules/car/css/styles.css",
		);
		
		$template->addStyle($css);

		// include all js files
		$js = array(
			/*array(
				"src" => "/modules/car/src/settings.js"
			),*/
			array(
				"src" => "modules/car/src/BaseComponent.js"
			),
			array(
				"src" => "/modules/car/src/FormParser.js"
			),
			array(
				"src" => "/modules/car/src/FormSubmission.js"
			),
			array(
				"src" => "/modules/car/src/DBQuery.js"
			),/*
			array(
				"src" => "/modules/car/src/EventFramework.js"
			),*/
			array(
				"src" => "/modules/car/src/car.js"
			),
			array(
				"src" => "/modules/car/src/InfiniteScroller.js"
			),
			array(
				"src" => "/modules/car/src/module.js"
			),
			array(
				"src" => "/modules/car/src/PageUI.js"
			)
		);

		$template->addScripts($js);

		return $template->render(array(
			"defaultStageClass" 	=> "not-home", 
			// "content" 						=> $form . $carResults . $cars, // OLD WAY
			"content" 						=> $carResults . $cars,
			"doInit"							=> false
		));
	}

	private function getListOptions($field) {
		$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
		$parsedResults = array();
		foreach($dbResults as $result) {
			$option = new stdClass();
			$option->name = $result[$field];
			$option->value = $result[$field];
			$parsedResults[] = $option;
		}
		return $parsedResults;
	}

	public function getSelectList($field) {
		$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
		$parsedResults = array();
		foreach($dbResults as $result) {
			$parsedResults[] = $result[$field];
		}
		return json_encode($parsedResults);
	}
	
	public function getCarResults() {
		// Takes raw data from the http request
		$json = file_get_contents('php://input');
		//$searchJson = json_encode(json_decode($json)[0]);

		$results = fetchCarsFromDb($json);

		if (count($results->rows) <= 0) { 
			print('<h4 style="text-align: center;">There are no results that match your search.</h4>');
			exit;
		}

		$config = array(
			'teaserWordLength' => 40, 'teaserCutoff' => 350, 'useTeasers' => true
		);
		$results = $this->formatResults($results, $config);
		Template::addPath(__DIR__ . "/templates");

		// Doesn't work
		// return Template::renderTemplate("case-reviews",array('cases'=>$results));

		$cars = Template::renderTemplate("case-reviews",array('cases'=>$results));
		print($cars);
		exit;
	}

	private function calculateDays($config) {
		$numOfMonths = $config["numOfMonths"];
		$inclusive = $config["inclusive"];

		$month = date("m") - $numOfMonths;
		$month = $month <= 0 ? 12 + $month : $month;

		$numOfYears = floor($numOfMonths / 12);
		
		$day = date("d");
		$year = date("Y") - $numOfYears;

		$d=mktime(0, 0, 0, $month, $day, $year);
		$days = floor((time() - $d)/60/60/24);

		if ($inclusive) $days += 1;

		return $days;
	}

	private function thisMonth() {
		return date("d");
	}

	private function thisYear() {
		$year = date("Y");
		$daysInYear = 0;

		for ($month = 1; $month < date("m"); $month++) {
			$daysInYear += cal_days_in_month(CAL_GREGORIAN, $month, $year);
		}

		$daysInYear += date("d");

		return $daysInYear;
	}

	private function formatResults($results, $config) {
		//Number of words to display in the teaser
		$teaserWordLength = $config['teaserWordLength'];
		//Minimun number of characters
		$teaserCutoff = $config['teaserCutoff'];
		$useTeasers = $config['useTeasers'];
		
		$cases = [];



		foreach($results as $result){

			$case = $result;

			$case["month"] = substr($case["month"], 0, 3);
			$case["month"] .= ".";

			$summaryArray =  explode(" " , $case["summary"]);
			$case['useTeaser'] = $useTeasers === true && strlen($case["summary"]) > $teaserCutoff;

			$case['teaser'] = implode(" ", array_slice($summaryArray, 0, $teaserWordLength));
			$case['readMore'] = implode(" ", array_slice($summaryArray, $teaserWordLength));

			$cases[] = $case;
		} 
		//iterable might be exhausted, may need to rewind here

		return $cases;
	}

	public function loadMore() {
		// Takes raw data from the http request
		$json = file_get_contents('php://input');
		//$searchJson = json_encode(json_decode($json)[0]);

		$results = fetchCarsFromDb($json);

		if (count($results->rows) <= 0) { 
			return null;
		}

		$config = array(
			'teaserWordLength' => 40, 'teaserCutoff' => 350, 'useTeasers' => true
		);
		$results = $this->formatResults($results, $config);
		Template::addPath(__DIR__ . "/templates");

		// Doesn't work
		// return Template::renderTemplate("case-reviews",array('cases'=>$results));

		return Template::renderTemplate("case-reviews",array('cases'=>$results));
		//return $cars;
	}

	function carCreate() {
		return carCreatePage();
	}

	function carSubmit() {
		submitNewCar();
	}

	function carUpdate($carId) {
		return carCreatePage($carId);
	}

	function carSubmitUpdate() {
		updateCar();
	}
}


function fetchCarsFromDb($json){
	$json = urldecode($json);
	$phpJson = json_decode($json);
	$conditions = array();
	$sortConditions = array();
	$limitCondition = "";

	//This removes queries that return everything
	foreach($phpJson as $cond) {
		if (is_array($cond) || ($cond->type == "condition" && $cond->value != "ALL")) {
			$conditions[] = $cond;
		} else if ($cond->type == "sortCondition") {
			$sortConditions[] = $cond;
		} else if ($cond->type == "limitCondition") {
			$limitCondition = $cond;
		}
	}
	/*
	if ($phpJson->value == "ALL") {
		return MysqlDatabase::query("SELECT * FROM car ORDER BY year DESC");
	}*/
	$builder = new QueryBuilder();
	$builder->setTable("car");
	$builder->setConditions($conditions);
	$builder->setSortConditions($sortConditions);
	$builder->setLimitCondition($limitCondition);
	$sql = $builder->compile();
	//print($sql);
	$results = MysqlDatabase::query($sql);
	//if results has an error returned as json
	$results->getIterator();
	return $results;
}