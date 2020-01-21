<?php

use \Html\HtmlLink;


class SollModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = sollRoutes();
		$this->name = "soll";
	}

}


function sollRoutes() {
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
	$req = new HttpRequest("https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019");
	
	$resp = $req->send();
	
	$page = new DocumentParser($resp->getBody());
	$fragment = $page->fromTarget("mw-content-text");
	

	$links = $fragment->getElementsByTagName("a");
	
	$aNumbers = array();
	
	for($i = 0; $i < 6; $i++) {
		$link = $links->item($i);
		$text = explode(" ", $link->nodeValue);
		$href = $link->getAttribute("href");
		if(strpos($href,"cdm") === false) continue;
		// print "<pre>".print_r($text,true)."</pre>";
		$defendant = $text[2];
		$plaintiff = $text[0];
		
		$aNumbers[] = loadANumbers($defendant, $plaintiff);
	}
	
	$resp = new HttpResponse($aNumbers[0]);
	$resp->setContentType("application/json; charset=utf-8");
	
	
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
