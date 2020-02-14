<?php

use \Html\HtmlLink;


class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarUrlParser.php","CarParserException.php","CarDB.php");
		$this->name = "car";
	}

}


function carRoutes() {
	return array(
		"search-soll" => array(
			"callback" => "loadANumbers",
			"Content-Type" => "application/json"
		),
		"load-cars" => array(
			"callback" => "loadCarsData",
			"Content-Type" => "application/json"
		),
		"view-page" => array(
			"callback" => "viewPage",
			"Content-Type" => "text/html"
		),
		"test-car-data" => array(
			"callback" => "testCarData",
			"Content-Type" => "application/json"
		),
		"test-car-urls" => array(
			"callback" => "testCandidateUrls",
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

	//$url = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";

	//Crate a new date formated to be passed to the CarUrlParser and pass it to the request object
	//Create new date from numeric values only
	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);
	$resp = $urlParser->makeRequests();

	//Pass the body of the page to the DocumentParser
	if($resp->getBody() != ""){
	$page = new DocumentParser($resp->getBody());
		//We are only concerned with the content located in the 'mw-content-text' class of the page
		$fragment = $page->fromTarget("mw-content-text");

		return $fragment;
	}
}

function viewPage($month,$day,$year){
	$page = loadPage($month,$day,$year);
	if($page == null){
		throw new CarParserException("The page at that url is null");
	}
	return $page->saveHTML();
}

function loadCarsData($xml){

	$subjects = $xml->getElementsByTagName("b");	
	
	$links = $xml->getElementsByTagName("a");

	$errors = array();
	$nullSubjects = array();
	
	$aNumbers = array();
	$cars = array();

	$MAX_PROCESS_LINKS = count($subjects)-1;
	
	for($i = 0; $i < $MAX_PROCESS_LINKS; $i++) {

		//We want to skip the first p tag which is the name of the person summarizing the cases
		$subject = $subjects->item($i+1);

		//We are skipping the first to links on the page because they are links to the author and the comments
		$link = $links->item($i+2);

		//if($subject != null && $link != null) //for testing purposes
		$car = new Car($subject,$link);	
		try{
			//if($car != null) //for testing purposes
			$car->parse();
		}catch(CarParserException $e){
			//do something with the $e->stuff
			$errors[] = $e;
			$nullSubjects[] = $subject;
		}
		$cars[] = $car;


	}
	// print("ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---");
	// var_dump($errors);
	// print("NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---");
	// var_dump($nullSubjects);
	
	return $cars;
}

function loadANumbers($defendant, $plaintiff = "State") {

	$searchTerm = $plaintiff."%20v.%20".$defendant;
	
	$fullUrl = "https://cdm17027.contentdm.oclc.org";
	$fullUrl .= "/digital/api/search/searchterm/{$searchTerm}/maxRecords/50";
	
	
	$req = new HttpRequest($fullUrl);
	
	$resp = $req->send();

	return $resp;
}




//route that takes an int number of days starting today tho attempth to load urls for without execution of calluserfunc line

function testCandidateUrls($days){
	set_time_limit(900);

	$output = array();

	//output = getoutput()

	//return output
	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$urlDate->modify("-1 day");
		$urlParser = new CarUrlParser($urlDate);
		$urlParser->makeRequests();
	}
}

//dump in sql format
function testCarData($days){
	set_time_limit(0);

	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$urlDate->modify("-1 day");
		//throw an exception if 2018
		$urlDateFormat = $urlDate->format("n j Y");
		$xml = call_user_func_array("loadPage",explode(" ",$urlDateFormat));

		if($xml == null){
			$status = "not found";
		}else{
			$cars = loadCarsData($xml);
			for($j = 0; $j < count($cars); $j++){
				$cn = $j+1;
				$date = $urlDate->format("F j, Y");
				print("<br><strong>-----CASE #".$cn." for ".$date."-----</strong><BR>");
				insert($cars[$j]);
				displayCarOutput($cars[$j]);
			}
			$status = $cars[$j]->url."everything went ok";
		}
		echo  nl2br ("THE CARS DATE: ".$urlDateFormat."---STATUS: ".$status."<br>");
	}
}

function getCarUrlsByDate($month = null,$day = null,$year = null) {
	set_time_limit(5);

	$urls = array("someUrl","someotherurl");

	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);


	$urls = $urlParser->getUrls();


	return $urls;
}
function getUrlsRange($days){

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

//move to url parser
function displayCarOutput($car){
	print("<strong>SUBJECT #1:</strong> ".$car->subject_1."<BR>");
	print("<strong>SUBJECT #2:</strong> ".$car->subject_2."<BR>");
	print("<strong>SUMMARY:</strong> ". $car->summary."<br>");
	print("<strong>CASE RESULT:</strong> ". $car->result."<br>");
	print("<strong>CASE TITLE:</strong>". $car->title."<br>");
	print("<strong>PLAINTIFF:</strong> ". $car->plaintiff."<br>");
	print("<strong>DEFENDANT:</strong> ". $car->defendant."<br>");
	print("<strong>CITATION:</strong> ". $car->citation."<br>");
	print("<strong>DECISION DATE:</strong> ". $car->month." ".$car->day.", ".$car->year."<br>");
	print("<strong>CIRCUT COURT:</strong> ". $car->circut."<br>");
	print("<strong>JUDGE:</strong> ". $car->majority."<br>");
	print("<strong>OTHER JUDGES:</strong> ". $car->judges."<br>");
	print("<strong>URL TO THE PAGE:</strong> ". $car->url."<br>");
}