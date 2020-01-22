<?php

use \Html\HtmlLink;


class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarPage.php");
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
	//library of defense protocol page object all the props of the url and method getAsUrl() method and pass the result to the 
	//httpRequest
	$req = new HttpRequest("https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019");
	
	$resp = $req->send();
	
	$page = new DocumentParser($resp->getBody());
	$fragment = $page->fromTarget("mw-content-text");

	$subjects = $fragment->getElementsByTagName("b");
	
	$links = $fragment->getElementsByTagName("a");
	
	$aNumbers = array();
	$cars = array();

	$MAX_PROCESS_LINKS = 10;
	
	for($i = 0; $i < $MAX_PROCESS_LINKS; $i++) {
		$car = new Car();
		$car->subject = explode(" - ",$subjects->item($i+1)->nodeValue)[0];
		$car->subSubject = explode(" - ",$subjects->item($i+1)->nodeValue)[1];
		$car->summary = getSummary($subjects->item($i+1));
		$car->result;
		$car->stateTitle;
		$car->citation;
		$car->decisionDate;
		$car->circutCourt;
		$car->circutCourtJudge;
		
		$link = $links->item($i);
		$text = explode(" ", $link->nodeValue);
		$href = $link->getAttribute("href");
		//if(strpos($href,"cdm") === false) continue;
		// print "<pre>".print_r($text,true)."</pre>";
		$defendant = $text[2];
		$plaintiff = $text[0];
		
		
		//$aNumbers[] = loadANumbers($defendant, $plaintiff);
		$cars[] = $car;

	}
	
	// $resp = new HttpResponse($aNumbers[0]);
	// $resp->setContentType("application/json; charset=utf-8");
	
	var_dump($cars); exit;
	return $resp;
}

function getSummary($subjectNode){
	$summaryNodes = array();
	$summary = "";
	$parent = $subjectNode->parentNode;
	$count = 0;

	if($parent->nodeName != "p"){
		throw new Exception("parent is not a p element");
	}else{
		//print($parent->nodeValue);
	}
	while(++$count < 10){// && null != ($next = $parent->nextSibling)){
		$next = $parent->nextSibling;
		$parent = $next;
		//$summaryNodes[] = $next->nodeName." NODEVALUE ".$next->nodeValue;
		if($next->nodeType == XML_TEXT_NODE) continue;
		//if($next->firstChild->nodeName == "b") break;
		if($next->firstChild->nodeName == "a") break;
		$summaryNodes[] = $next->nodeValue;
	}
	//var_dump($summaryNodes);exit;
	return implode("\n",$summaryNodes);
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
