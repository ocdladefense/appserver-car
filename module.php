<?php

use \Html\HtmlLink;


class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarUrlParser.php","CarParserException.php");
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
		"test-urls" => array(
			"callback" => "testUrls",
			"Content-Type" => "application/json"
		)
	);
}

function loadPage($month,$day,$year) {

	//$url = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";

	//Crate a new date formated to be passed to the CarUrlParser and pass it to the request object
	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);
	$url = $urlParser->toUrl();

	$req = new HttpRequest($url);
	
	$resp = $req->send();

	if($resp->getStatusCode() != 200){
		return null;
	}

	//Pass the body of the page to the DocumentParser
	$page = new DocumentParser($resp->getBody());
	//We are only concerned with the content located in the 'mw-content-text' class of the page
	$fragment = $page->fromTarget("mw-content-text");

	return $fragment;
}

function viewPage($month,$day,$year){
	$page = loadPage($month,$day,$year);
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

function testUrls(){
	$urlDate = new DateTime();
	for($i = 0; $i < 16 ; $i++){
		$urlDate->modify("-1 day");
		$urlDateFormat = $urlDate->format("n j Y");
		$xml = call_user_func_array("loadPage",explode(" ",$urlDateFormat));

		if($xml == null){
			$status = "not found";
		}else{
			$cars = loadCarsData($xml);
			for($i = 0; $i < count($cars); $i++){
				$cn = $i+1;
				$date = $urlDate->format("F j, Y");
				print("<br>-----CASE #".$cn." for ".$date."-----<BR>");
				print("SUBJECT #1: ".$cars[$i]->getSubjects()[0]."<BR>");
				print("SUBJECT #2: ".$cars[$i]->getSubjects()[1]."<BR>");
				print("SUMMARY: ". $cars[$i]->getSummary()."<br>");
				print("CASE RESULT: ". $cars[$i]->getCaseResult()."<br>");
				print("CASE TITLE: ". $cars[$i]->getCaseTitle()."<br>");
				print("PLAINTIFF: ". $cars[$i]->getLitigants()[0]."<br>");
				print("DEFENDANT: ". $cars[$i]->getLitigants()[1]."<br>");
				print("CITATION: ". $cars[$i]->getCitation()."<br>");
				print("DECISION DATE: ". $cars[$i]->getDecisionDate()."<br>");
				print("CIRCUT COURT: ". $cars[$i]->getCircutCourt()."<br>");
				print("JUDGE: ". $cars[$i]->getJudge()."<br>");
				print("OTHER JUDGES: ". $cars[$i]->getOtherJudges()."<br>");
			}
			$status = "everything went ok";
		}
		echo  nl2br ("THE CARS DATE: ".$urlDateFormat."---STATUS: ".$status."<br>");
	}
}