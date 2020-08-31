<?php

use \Html\HtmlLink;
use function \Html\createElement as createElement;


define("DOM_SECTION_BREAK","<p>&nbsp;</p>");

define("DOM_COMMA",",");

define("DOM_LINE_BREAK","<br />");

define("DOM_SPACE"," ");




class CarModule extends Module {

	protected $routes = array(
		"cars"		=> array(
			"callback" => "home",
			"Content-Type" => "text/html"
			//"access" => true
		),
		"car-form" => array(
			"callback" => "edit",
			"Content-Type" => "application/json"
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
		),
		"car-delete" => array(
			"callback" => "carDelete",
			"Content-Type" => "text/html"
		),
		"car-delete-submit" => array(
			"callback" => "carDeleteSubmit",
			"Content-Type" => "text/html"
		)
	);
		
	protected $files =  array(
		"GenericDb.php", // @todo, move this somewhere to core/includes.
		"CaseReviewsDb.php",
		"SearchForm.php",
		"CaseReviewsListTemplate.php",
		"CaseReviewForm.php"
	);
		
	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
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
	public function home() {
	
		// Much closer to HttpRequest class here.
		// Make sure to do any related request processing in the *route.
		$this->request->getBody();
		
		return $this->getFirstPage();
	}
	
	

	
	public function getPage($page = 1, $withForm = true) {
		// Prepare data for the template.
		$db = new CaseReviewsDb();
		
		$tpl = new CaseReviewsTemplate("case-reviews");
		$tpl->addPath(__DIR__ . "/templates");


		$tpl->formatResults($db->select(), array(
			"teaserWordLength" => 40, "teaserCutoff" => 350, "useTeasers" => true));

		// Return something that can be converted into a string!
		return !$withForm ? $tpl : $tpl->bind(new SearchForm());
	}
	
	
	
	public function getFirstPage($withForm = true) {
		return $this->getPage(1, $withForm);
	}
	
	public function nextPage() {
		$json = file_get_contents('php://input');
		
		return $this->loadRecords($json);
	}	
	
	public function getLastPage($withForm = false) {
		$pages = 10;
		
		return $this->getPage($pages - 1, $withForm);
	}


	




	// Find a better name?
	// For now indicates the user's intent to "edit" something.
	// Should return form metadata for client-side scripts.
	function edit() {
		$json = new CaseReviewForm();
		// Do some other stuff configuring the form...
		
		return $json;
	}

	/**
	 * Either update or insert a CAR record.
	 *
	 */
	function save() {
		submitNewCar();
	}
	
	function update($carId) {
		// updateCar(); // previously
		$json = file_get_contents('php://input');
		// $this->request->getBody();
		$json = urldecode($json);
		$phpJson = json_decode($json);	
		
		$db = new CaseReviewsDb();
		$db->update($phpJson);
		
		// What to return?  Some kind of 
	}

	function insert() {
		return carCreatePage($carId);
	}

	// @todo callout to CaseReviewsDb.
	function delete() {
		
	}
	
	



}


