<?php

use Mysql\Database;
use Mysql\DbHelper;
use Mysql\QueryBuilder;
use Http\HttpRequest;
use Http\HttpHeader;
use Http\HttpHeaderCollection;
use GIS\Political\Countries\US\Oregon;
use Ocdla\Date;


use function Mysql\insert;
use function Mysql\update;
use function Mysql\select;





class CarModule extends Module {


	// API name of the object.
	// Objects can register their own methods
	// for handling CRUD operations.
	protected $object = "car";





	public function __construct() {
	
		parent::__construct(__DIR__);

		$this->name = "car";
	}


	
	public function getJudges() {

		$appellate = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trial = DbHelper::getDistinctFieldValues("car", "appellate_judge");


		

		$judges = array_merge($appellate,$trial);
		// var_dump($judges);
		$values = array();
		foreach($judges as $judge) {
			$arr = preg_split("/[,;\s\.]/", $judge);
			$values = array_merge($values,$arr);
		}


		// var_dump($values);

		$filter = function($judge) {
			return strlen($judge) >= 4
			  && !in_array($judge,["dissenting","concurring","curiam","part"]);
		};

		$values = array_filter($values, $filter);

		return array_unique($values);
	}

	/**
	 * @method showCars
	 * 
	 * Show our CAR page with a list of CAR entries
	 * that resulted from the query.  Default query is to show all CARs,
	 * with the most recent CARs first.  The page includes a search widget, a message for
	 * the user summarizing the results of their query and the 
	 * actual list of CAR entires.
	 * 
	 * @param $carId
	 */
	public function getList($recordId = null) {

		$query = $this->getQuery();
		// var_dump($query);
		$records = select($query);

		if(!is_array($records)) $records = array($records);


		// If there is a new car show it at the top of the list.
		if(!empty($carId)) {

			$promote = select("SELECT * FROM car WHERE id = '$recordId'");

			$promote->isNew(true);

			for($i = 0; $i < count($records); $i++){

				if($records[$i]->getId() == $promote->getId()){
	
					unset($records[$i]);
				}
			}

			array_unshift($records, $promote);
		}


		$tpl = new Template("list");
		$tpl->addPath(__DIR__ . "/templates");

		$params = !empty($_GET) ? $_GET : $_POST;
		//var_dump($params);exit;
		$summarize = "1" === $params["summarize"];
		$list = $tpl->render(array(
			"summarize" => $summarize,
			"records" => $records
		));

		
		$tpl = new Template("page");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"list" => $list,
			"query" => $query,
			"count" => count($records),
			"results" => count($records) > 0
		));
	}






	private function getQuery() {


		$user = current_user();

		$conditions = array(
			"op" => "AND",
			"conditions" => array(
				array(
					"fieldname"	=> "subject",
					"op"		=> "LIKE",
					"syntax"	=> "'%s%%'"
				),
				array(
					"fieldname"	=> "county",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "appellate_judge",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "trial_judge",
					"op"		=> "LIKE",
					"syntax"	=> "'%%%s%%'"
				),
				array(
					"fieldname"	=> "court",
					"op"		=> "=",
					"syntax"	=> "'%s'"
				),
				array(
					"fieldname"	=> "importance",
					"op"		=> "=",
					"syntax"	=> "%s"
				)
			)
		);

		$params = !empty($_GET) ? $_GET : $_POST;
		//var_dump($params);exit;
		$summarize = "1" === $params["summarize"];
		// var_dump($params);exit;
		$sql = new QueryBuilder("car");

		$sql->setFields(array("*"));

		if(!empty($params)) $sql->setConditions($conditions, $params);
		if(!empty(trim($params["year"]))) $sql->addCondition("YEAR(decision_date)={$params['year']}");
	
		$sql->setOrderBy($summarize ? "subject, decision_date DESC" : "decision_date DESC");

		return $sql->compile();
	}








	public function showRecordForm($recordId = null) {

		$class = ucwords($this->object);

		$record = !empty($recordId) ? select("SELECT * FROM {$this->object} WHERE id = '$recordId'") : new $class();

		$importance = !empty($record->getImportance()) ? $record->getImportance() : "";

		// Build the list of courts and set the selected court.
		$courts = Oregon::getCourts();
		$courts[""] = "None Selected";
		$court = empty($record->getCourt()) ? "" : $record->getCourt();


		// Build the list of subjects and set the selected subject.
		$subjects = DbHelper::getDistinctFieldValues($this->object, "subject");
		$subjects = array_map(function($subject) { return ucwords($subject); }, $subjects);
		$subjectDefault = array("" => "None Selected");
        $subjects = $subjectDefault + $subjects;
		$subject = empty($record->getSubject()) ? "" : $record->getSubject();
        $subject = ucwords($subject);


		// Build the list of counties and set the selected county.
		$counties = Oregon::getCounties();
		$countyDefault = array("" => "None Selected");
        $counties = $countyDefault + $counties;
        $county = empty($record->getCircuit()) ? "" : $record->getCircuit();


		// Set other variables.
		$flagged = $record->isFlagged() ? "checked" : "";
        $draft = $record->isDraft() ? "checked" : "";



		$tpl = new Template("form");
		$tpl->addPath(__DIR__ . "/templates");


		$data = '{"trial":["name1","name2"],"sentencing":["s1","s2"]}';

		$judges = json_decode($data);

		var_dump($judges);exit;

		return $tpl->render(array(
			"record" 			=> $record,
			"importance"		=> $importance,
			"court" 			=> $court,
			"courts"	   		=> $courts,
			"subject" 			=> $subject,
			"subjects" 			=> $subjects,
			"county" 			=> $county,
			"counties" 			=> $counties,
			"judges"   			=> $this->getJudges(),
			"alt_judges"		=> $judges,
			"flagged"	 		=> $flagged,
			"draft" 			=> $draft
		));
	}



	public function save() {

		$req = $this->getRequest();
		$input = (array) $req->getBody();

		$sobject = $this->object;


		// How do we blank out a value?
		foreach($input as $key => $value){

			if(empty($value)) unset($record[$input]);
		}

		$record = $sobject::from_array_or_standard_object($input);

		return empty($record->getId()) ? $this->create($record) : $this->update($record);
	}



	// public function create(SObject $record).
	public function create($record) {

		$result = insert($record);

		return redirect("/{$this->object}/list/{$record->getId()}");
	}
	
	
	
	
	// For now only allow updates on test reviews.
	public function update($record) {
	
		$result = update($record);

		return redirect("/{$this->object}/list/{$record->getId()}");
	}




	public function delete($id){

		$record = select("SELECT * FROM {$this->object} WHERE id = '$id'");

		$query = "DELETE FROM {$this->object} WHERE Id = '$id'";

		$db = new Database();

		$result = $db->delete($query);

		return redirect("/{$this->object}/list");
	}




	public function flag() {

		$req = $this->getRequest();
		$body = $req->getBody();

		
		$id = $body->id;
		$bool = $body->is_flagged;

		$query = "UPDATE {$this->object} SET is_flagged = $bool WHERE id = '$id'";

		$database = new Database();
		$result = $database->update($query);

		return "success";
	}
	


}


function recordPreprocess($record) {

	static $index = 0; 


	$classes = array();

	if($record->isNew()) $classes[] = "is-new";

	$classes = implode(" ", $classes);

	$fix = preg_replace("/[\n\r]+/","\n", $record->getSummary());
	// var_dump($fix);
	// $fix = str_replace("\n\n","\n",  $fix);
	// $fix = str_replace("\n\n","\n",  $fix);
	$fix = str_replace("\n","</p><p>", $fix);
	$fix = "<p>" . $fix . "</p>";

	return array(
		"car" 					=> $record,
		"summary" 				=> $fix,
		"county"				=> $record->getCounty(),
		"date"					=> $record->getDate(),
		"subject"				=> ucwords($record->getSubject()),
		"flagged"				=> $record->isFlagged(),
		"secondary_subject" 	=> $record->getSubject2(),
		"counter" 				=> $index++,
		"classes" 				=> $classes,
		"title" 				=> $record->getTitle(),
		"rank"					=> $record->getImportance(),
		"importance" 			=> !empty($record->getImportance()) ? $record->getImportance() . "/5" : "unset",
		"court" 				=> $record->getCourt()
	);
}