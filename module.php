<?php

use \Html\HtmlLink;
use function \Html\createElement as createElement;


define("DOM_SECTION_BREAK","<p>&nbsp;</p>");

define("DOM_COMMA",",");

define("DOM_LINE_BREAK","<br />");




class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarUrlParser.php","CarParserException.php","CarDB.php","CarIterator.php","CarParserStatus.php");
		$this->name = "car";
	}



	/**
	 * @route "/cars"
	 *
	 * @description Show a list of CARs together with a search form.
	 *  CAR data is stored in a database.  After loading the data we can build the
	 *  HTML page using the new case-reviews template.
	 *
	 * @return String The HTML markup, including the case reviews.
	 */
	public function carSearchForm($params = array()) {


		//Hard coded for testing only!!!  Comment this out to use paramenters!!!
		// $params = '[{"field":"summary","op":"LIKE","value":"duii"},
		// {"field":"result","op":"LIKE","value":"reversed"},
		// {"field":"subject_2","op":"LIKE","value":"discretionary"},
		// {"field":"year","op":"=","value":2019}]';




		
		// Will already search in default location,
		//  So let's add a pointer to our module's templates.
		Template::addPath(__DIR__ . "/templates");
		
		
		// Perform a query for CARs in the database.
		// @todo - should return an iterable list of SObjects.

		//if conditons have been passed in then use them to build the query
		//otherwise use the next line for the query
		if(!empty($params)){
			$builder = new QueryBuilder();
			$builder->setTable("car");
			$builder->setConditions(json_decode($params));
			$sql = $builder->compile();
			//print($sql);exit;
		
			$results = MysqlDatabase::query($sql);
			//if results has an error returned as json
			//$results = $results->getIterator();
		}
		else {
			$results = MysqlDatabase::query("SELECT * FROM car ORDER BY year DESC");
		}
		
		
		
		// Templates to generate our HTML.
		$form = $this->getSearchForm();
		$template = Template::loadTemplate("webconsole");
		$cars = Template::renderTemplate("case-reviews",array('cases'=>$results));
		

		// ... and custom styles.
		$css = array(
			"active" => true,
			"href" => "/modules/car/css/styles.css"
		);
		
		$template->addStyle($css);

		// include all js files
		$js = array(
			array(
				"src" => "/modules/car/src/module.js"
			),
			array(
				"src" => "/modules/car/src/CarFormParser.js"
			),
			array(
				"src" => "/modules/car/src/settings.js"
			),
			array(
				"src" => "/modules/car/src/car.js"
			)
		);

		$template->addScripts($js);

		
		return $template->render(array(
			"defaultStageClass" 	=> "not-home", 
			"content" 						=> $form . $cars,
			"doInit"							=> false
		));
	}
	private function getSearchForm() {
		$form = "<h2>OCDLA Criminal Apellate Review Search</h2>";
		$form .= "<h5>Showing all results:</h5>";

		$form .= $this->buildSelect("subject_1");
		$form .= createElement("input", ["id" => "car-search-box", "placeholder" => "Search case review"], []);

		return $form;
	}

	private function buildSelect($field) {
		$optionStrings = $this->getListOptions($field);
		$createOption = function($option) {
			return createElement("option", ["value" => $option], $option);
		};

		$optionElements = array_map($createOption, $optionStrings);

		return createElement("select", ["id" => "car-subject"], $optionElements);		
	}

	private function getListOptions($field) {
		$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
		$parsedResults = array();
		foreach($dbResults as $result) {
			$parsedResults[] = $result[$field];
		}
		return $parsedResults;
	}


}


function carRoutes() {
	return array(
		"cars"		=> array(
			"callback" => "carSearchForm",
			"Content-Type" => "text/html"
		),
		"load-cars" => array(
			"callback" => "loadCarsData",
			"Content-Type" => "application/json"
		),
		"view-page" => array(
			"callback" => "viewPage",
			"Content-Type" => "text/html"
		),
		"insert-bulk-case-reviews" => array(
			"callback" => "insertBulkCarData",
			"Content-Type" => "text/html"
		),
		"insert-single-case-reviews" => array(
			"callback" => "insertCarDataForDay",
			"Content-Type" => "text/html"
		),
		"test-car-urls" => array(
			"callback" => "getCandidateUrlOutput",
			"Content-Type" => "application/json"
		),
		"car-urls" => array(
			"callback" => "getCarUrlsByDate",
			"Content-Type" => "application/json"
		),
		"car-urls-range" => array(
			"callback" => "getUrlsRange",
			"Content-Type" => "application/json"
		)
	);
}


function loadPage($month,$day,$year) {

	//Crate a new date formated to be passed to the CarUrlParser and pass it to the request object
	//Create new date from numeric values only
	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));

	$urlParser = new CarUrlParser($urlDate);
	$urlParser->setMaxUrlTests(6);
	$urlParser->makeRequests();
	return $urlParser;

}

function viewPage($month,$day,$year){
	$page = loadPage($month,$day,$year);
	if($page == null){
		throw new CarParserException("The page at that url is null");
	}
	return $page->saveHTML();
}

function loadCarsData($xml,$url){
	if($xml->isDraft()){
		return array();
	}


	$authorNotSubjectCount = 1;
	//We are skipping the first to links on the page because they are links to the author and the comments
	$linksToSkip = 2;
	$subjects = new CarIterator($xml->getElementsByTagName("b"));

	//We want to skip the element with info about the author of the case review summarizations
	$skip = function($text){
		return strpos($text,"Summarized") !== false; 
	};

	$subjects->setTest($skip);

	$links = new CarIterator($xml->getElementsByTagName("a"));
	$links->skip($linksToSkip);

	$errors = array();

	$cars = array();

	foreach($subjects as $subject){

		$car = new Car($subject,$links->current(),$url);	

		try{
			$car->parse();
		}catch(CarParserException $e){
			$errors[] = $e;
		}
		
		$cars[] = $car;
		$links->next();
	}
	return $cars;
}

function insertBulkCarData($days){
	$startTime = time();
	$statuses = array();

	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$status = new CarParserStatus();
		$status->setRuntime(time() - $startTime);

		$cars = array(); // An array of Car objects for this day.

		$urlDate->modify("-1 day");
		//throw an exception if 2018
		$urlDateFormat = $urlDate->format("n j Y");
		$status->setDate($urlDate);
		$parser = call_user_func_array("loadPage",explode(" ",$urlDateFormat));
		$xml = $parser->getDocumentParser();

		if($xml == null){
			$status->setMessage("not found");
		} else {
			try{
				$cars = loadCarsData($xml,$parser->getSelectedUrl());
				// This is the GLOBAL insert call.
				if(count($cars) !== 0){
					insert($cars);
				}
				//var_dump($cars);
				$status->setMessage("everything went ok");
				$status->setUrl($parser->getSelectedUrl());
			} catch(DbException $e){
				$status->setUrl($parser->getSelectedUrl());
				$status->setMessage($e->getMessage());
				$status->setStatusCode("DB_INSERT_ERROR");
			} 
		}
		$statuses[] = $status;
	}


	Template::addPath(__DIR__ . "/templates");
	$html = Template::renderTemplate("car-parser-statuses",array('statuses'=>$statuses));

	// ... and custom styles.
	$css = array(
		"active" => true,
		"href" => "/modules/car/css/styles.css"
	);
	
	//$template->addStyle($css);
	

	return $html;
	// displayErrors($errors);
}

function insertCarDataForDay($month,$day,$year){
	$statuses = array();
	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));

	$xml = loadPage($month,$day,$year);

	$status = new CarParserStatus();

	$cars = array(); // An array of Car objects for this day.
	$urlDateFormat = $urlDate->format("n j Y");
	$status->setDate($urlDate);
	$parser = call_user_func_array("loadPage",explode(" ",$urlDateFormat));
	$xml = $parser->getDocumentParser();

	if($xml == null){
		$status->setMessage("not found");
	} else {
		try{
			$cars = loadCarsData($xml,$parser->getSelectedUrl());
			// This is the GLOBAL insert call.
			if(count($cars) !== 0){
				insert($cars);
			}
			//var_dump($cars);
			$status->setMessage("everything went ok");
			$status->setUrl($parser->getSelectedUrl());
		} catch(DbException $e){
			$status->setUrl($parser->getSelectedUrl());
			$status->setMessage($e->getMessage());
			$status->setStatusCode("DB_INSERT_ERROR");
		} 
	}
	$statuses[] = $status;

	Template::addPath(__DIR__ . "/templates");
	$html = Template::renderTemplate("car-parser-statuses",array('statuses'=>$statuses));

	// ... and custom styles.
	$css = array(
		"active" => true,
		"href" => "/modules/car/css/styles.css"
	);
	
	//$template->addStyle($css);
	

	return $html;
	// displayErrors($errors);
}



//----------Testing Functions-----------------
//route that takes an int number of days starting today tho attempts to load urls for without execution of calluserfunc line
function getCandidateUrlOutput($days){
	set_time_limit(900);

	$output = array();

	//output = getoutput()

	//return output
	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$urlDate->modify("-1 day");
		$urlParser = new CarUrlParser($urlDate);
		$urlParser->makeRequests();	
		$output[] = $urlParser->getOutput();
	}
	return $output;
}
function getCarUrlsByDate($month = null,$day = null,$year = null) {
	set_time_limit(5);

	$urls = array();

	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);


	$urls = $urlParser->getUrls();


	return $urls;
}
function getUrlsRange($days){
	//gets the candidate urls for a specified number of days

	$date = new DateTime();
	$urls = array();

	for($i = 0; $i <= $days; $i++){
		$date->modify("-1 day");
		$urlParser = new CarUrlParser($date);


		//Standard class to hold metadata for the urls  
		$data =  new StdClass();
		$data->date = $date->format("F j, Y");
		$data->iterationNumber = $i;
		$data->urls = $urlParser->getUrls();

		$urls[] = $data;

	}
	
	return $urls;
}
