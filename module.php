<?php

use \Html\HtmlLink;
use function \Html\createElement as createElement;
//use Http;


define("DOM_SECTION_BREAK","<p>&nbsp;</p>");

define("DOM_COMMA",",");

define("DOM_LINE_BREAK","<br />");

define("DOM_SPACE"," ");




class CarModule extends Module {


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
		$this->loadLimit = 10;
	}

	/**
	 * @method sayHello
	 *
	 * @description Provides an example method
	 *  to demonstrate that this module can be consumed
	 *  outside of a web request.
	 */
	public function sayHello() {
		return "Hello World!";
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
	public function home() {
	
		// Much closer to HttpRequest class here.
		// Make sure to do any related request processing in the *route.
		$this->request->getBody();
		
		return $this->getFirstPage();
	}
	
	
	public function getFirstPage($withForm = true) {
		return $this->getPage($withForm);
	}
	
	 
	public function getPage($withForm = true, $json = null) {
		$loadLimit = $this->loadLimit;

		// Prepare data for the template.
		$builder = QueryBuilder::fromJson($json);
		
		$tpl = new CaseReviewsTemplate("case-reviews");
		$tpl->addPath(__DIR__ . "/templates");

		// $results = MysqlDatabase::query($builder->compile());
		$results = MysqlDatabase::query("select * from car limit 10");
		
		$tpl->formatResults($results, array(
			"teaserWordLength" => 40, "teaserCutoff" => 350, "useTeasers" => true));

		// Return something that can be converted into a string!
		// In this case any instance of Template can be returned.
		//  It will be automatically rendered with the bound variables
		//   and then converted into a string and packaged up as part
		//   of the HttpResponse.
		return !$withForm ? $tpl : $tpl->bind(new SearchForm($loadLimit));
	}
	
	
	

	
	
	
	public function nextPage($withForm = false) {
		//$db = new CaseReviewsDb();
		$json = $this->request->getBody();
		//$page = $db->getNextPage($json);

		return $this->getPage(1, $withForm, $json);
	}	
	
	
	
	public function getLastPage($withForm = false) {
		$db = new CaseReviewsDb();
		$json = $this->request->getBody();
		$page = $db->getNumOfPages($json);
		
		return $this->getPage($page, $withForm, $json);
	}

	function getResultsFromPage($page, $results, $perPage) {
		$pageResults = array();
		
		$start = ($page - 1) * $perPage;
		$end = $start + $perPage;
		$i = 0;
		foreach ($results as $result) {
			if ($i >= $start && $i < $end) {			
				$pageResults[] = $result;
			}
			$i++;
		}

		return $pageResults;
	}
	


	/**
	 * Updated: function should now be called:
	 *  like /car/1
	 *   Can't find the Car class.
	 */
	function getCar($carId) {

		// $carId = $this->request->getBody(); // @jbernal - old way to retireve id.
		//  now get passed in as the param by the framework.
		return new Car($carId);
	}



	// Find a better name?
	// For now indicates the user's intent to "edit" something.
	// Should return form metadata for client-side scripts.
	function edit() {
		return new CaseReviewForm();
	}
	
	
	


	/**
	 * Either update or insert a CAR record.
	 *
	 */
	function save() {
		submitNewCar();
	}



	function select() {
		$json = file_get_contents('php://input');

		$db = new CaseReviewsDb();
		
		$results = $db->select($json);
	}
	
	
	
	function update() {
		// updateCar(); // previously
		$json = $this->request->getBody();
		
		$db = new CaseReviewsDb();
		$db->update($json);
		
		// What to return?  Some kind of 
	}



	function insert() {
		$json = $this->request->getBody();
		
		$db = new CaseReviewsDb();
		$db->insert($json);
	}




	// @todo callout to CaseReviewsDb.
	function delete() {
		$json = $this->request->getBody();
		
		$db = new CaseReviewsDb();

		return $db->delete($json);
	}
	
	



}


