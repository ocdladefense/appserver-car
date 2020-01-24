<?php

use \Html\HtmlLink;


class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarUrlParser.php");
		$this->name = "car";
	}

}


function carRoutes() {
	return array(
		"search-soll" => array(
			"callback" => "loadANumbers",
			"Content-Type" => "application/json"
		),
		"load-car" => array(
			"callback" => "loadCar",
			"Content-Type" => "application/json"
		)
	);
}

// https://trust.ocdla.org/car/State/McCurry


/**
 * $req = new HttpRequest("https://www.oregonlaws.org/ors/137.700");

		$req->xml();


		new ClassFinder("nav nav-tabs hidden-print");

		$title = new Label(".section_title"); // Get the section title label for display in the sidebar

		new MainContent("#text");
		new MainContent("#annotations");
		new MainContent("#related-statutes");
		// https://www.oregonlegislature.gov/bills_laws/Pages/2011-ORS-Preface.aspx
*/
function loadCar() {
	$url = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";
	
	// $urlParser = new CarUrlParser($url);
	// $uParser = $urlParser->getAsUrl();
	// print($url);exit;

	//library of defense protocol page object all the props of the url and method getAsUrl() method and pass the result to the 
	//httpRequest

	// $today = today();
	// for($i = 0; $i < 365; $i++){
	// 	$date = $date ?: new Data($date).subtractDays();
	// 	$parser = new CarUrlParser::forDate($date);
	// 	$url = $parser->toUrl();
	// }


	$req = new HttpRequest($url);
	
	$resp = $req->send();
	// if($resp->status != 200) continue;
	$page = new DocumentParser($resp->getBody());
	$fragment = $page->fromTarget("mw-content-text");

	//$fragment->setSelector("b");
	//for 2011 case reviews $fragment->setSelector(".mw-headline")
	$subjects = $fragment->getElementsByTagName("b");
	
	$links = $fragment->getElementsByTagName("a");
	
	$aNumbers = array();
	$cars = array();

	$MAX_PROCESS_LINKS = 5;

	//subjects->item() and Links
	
	for($i = 0; $i < $MAX_PROCESS_LINKS; $i++) {

		 //We want to skip the first p tag which is who is summarizing the cases
		$subject = $subjects->item($i+1);

		 //We are skipping the first to links on the page because they are links to the author and the comments
		$link = $links->item($i+2);

		$car = new Car($subject,$link);		
		$car->parse();


		// $link = $links->item($i);
		// $text = explode(" ", $link->nodeValue);
		// $href = $link->getAttribute("href");
		//if(strpos($href,"cdm") === false) continue;
		
		
		//$aNumbers[] = loadANumbers($defendant, $plaintiff);
		$cars[] = $car;

	}
	
	// $resp = new HttpResponse($aNumbers[0]);
	// $resp->setContentType("application/json; charset=utf-8");
	
	var_dump($cars); exit;
	return $resp;
}

function loadANumbers($defendant, $plaintiff = "State") {
	// https://cdm17027.contentdm.oclc.org/digital/search/searchterm/State%20v.%20McCurry
	// https://trust.ocdla.org/car/State/McCurry
	// $plaintiff = "State";
	// $defendant = "McCurry";
	$searchTerm = $plaintiff."%20v.%20".$defendant;
	
	$fullUrl = "https://cdm17027.contentdm.oclc.org";
	$fullUrl .= "/digital/api/search/searchterm/{$searchTerm}/maxRecords/50";
	
	
	$req = new HttpRequest($fullUrl);
	
	$resp = $req->send();


	return $resp;
}
