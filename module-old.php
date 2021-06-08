<?php

use Mysql\Database;

class CarModule extends Module {


	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
		$this->loadLimit = 10;
	}

	public function showCars() {

		$filter = !empty($_POST["filter"]) ? $_POST["filter"] : null; 

		$cars = $this->getCars($filter);

		$subjects = $this->getSubjects();

		$tpl = new Template("car-list");
		$tpl->addPath(__DIR__ . "/templates");


		return $tpl->render(array(
				"cars" 				=>  $cars,
				"subjects" 			=> $subjects,
				"filter" 			=> $filter
		));
	}

	public function getCars($filter = null) {

		$yesFilter = "SELECT * FROM car WHERE subject_1 LIKE '%$filter%' ORDER BY isFlagged DESC, Year DESC, Month DESC, Day DESC";

		$noFilter = "SELECT * FROM car ORDER BY isFlagged DESC, Year DESC, Month DESC, Day DESC";

		$result = $filter == null ? Database::query($noFilter) : Database::query($yesFilter);

		$records = $result->getIterator();

		$cars = array();
		foreach($records as $record){

			$cars[] = Car::from_query_result_record($record);
		}

		return $cars;
	}


	public function getSubjects() {

		$result = Database::query("SELECT subject_1 FROM car ORDER BY subject_1");

		$records = $result->getIterator();

		$subjects = array();

		foreach($records as $subject) {

			$subject = trim($subject["subject_1"]);

			if(!in_array($subject, $subjects)){

				$subjects[] = $subject;
			}
		}

		return $subjects;
	}

	public function flagReview(){

		$req = $this->getRequest();
		$body = $req->getBody();

		$table = $body->tableName;
		$id = $body->carId;
		$isFlagged = $body->isFlagged;

		$query = "UPDATE $table SET isFlagged = $isFlagged WHERE Id = '$id'";

		$database = new Database();
		$result = $database->update($query);

		return "success";
	}

	public function showCarForm($carId = null){

		$tpl = new Template("car-form");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl;
	}

	public function upsertCar(){

		var_dump("upsert car");

		exit;
	}
	
	
	public function testCarRoute(){

		return "Hello World!";
	}


	///////////////////////////////			OLD?		///////////////////////////////////////////////////////////////////
	


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
		//$results = MysqlDatabase::query("select * from car limit 10");
		$results = $this->select($json);
		
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

		return $this->getPage($withForm, urldecode($json));
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



	public function select($json = null) {

		if($json === null) {

			return MysqlDatabase::query("SELECT * FROM car ORDER BY full_date DESC LIMIT 10");
		}

		$builder = QueryBuilder::fromJson($json);
		$builder->setTable("car");
		$builder->setType("select");
		$sql = $builder->compile();

		return MysqlDatabase::query($sql);
	}
	
	
	
	public function update() {

		// updateCar(); // previously
		$json = $this->request->getBody();
		$json = urldecode($json);
		
		$builder = QueryBuilder::fromJson($json);
		$builder->setTable("car");
		$builder->setType("update");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "update");

		//return $results;
	}



	public function insert() {

		$json = $this->request->getBody();
		$json = urldecode($json);

		$builder = QueryBuilder::fromJson($json);
		$builder->setTable("car");
		$builder->setType("insert");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "insert");

		//return $results;
	}




	// @todo callout to CaseReviewsDb.
	public function delete() {

		$json = $this->request->getBody();
		$json = urldecode($json);

		$builder = QueryBuilder::fromJson($json);
		$builder->setTable("car");
		$builder->setType("delete");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "delete");

		return $results;
	}

}

