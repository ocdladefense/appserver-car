<?php

use Mysql\Database;
use Http\HttpRequest;
use Http\HttpHeader;
use Mysql\DbHelper;
use Mysql\QueryBuilder;
use Http\HttpHeaderCollection;
use GIS\Political\Countries\US\Oregon;
use Ocdla\Date;

use function Html\createDataListElement;
use function Html\createSelectElement;




class SearchWidget extends Presentation\Component {

	private $data;


	public function __construct($name) {

		parent::__construct($name);
		$this->template = "car-search";
	}




	public function getSubjects() {

		$subjects = DbHelper::getDistinctFieldValues("car", "subject");
		$subjects = array_map(function($subject) { return ucwords($subject); }, $subjects);
	
		$subjectDefault = array("" => "All Subjects");
		$allSubjects = $subjectDefault + $subjects;

		$subject = "Crimes";

		return createSelectElement("subject", $allSubjects, $subject);
	}


	public function getYears() {

		$yearDefault = array("" => "All Years");
		$allYears = $yearDefault + $years;
	}


	public function getCounties() {
		$countyDefault = array("" => "All Counties");
		$allCounties = $countyDefault + $counties;
	}


	
	public function getJudges() {
		print createDataListElement("judge-datalist", $judges);
	}



	public function getImportance() {
		$importanceLevels = array(
			"" => "All Importance",
			1 => "1",
			2 => "2",
			3 => "3",
			4 => "4",
			5 => "5"
		);
	}

	public function isSummary() {
		$summarizeChecked = $doSummarize ? "checked" : "";
	}


	public function getCarSearch($params, $query) {

		$subjects = DbHelper::getDistinctFieldValues("car", "subject");

		

		$years = DbHelper::getDistinctFieldValues("car", "year");

		$appellateJudges = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trialJudges = DbHelper::getDistinctFieldValues("car", "trial_judge");

		$allJudges = array_merge($appellateJudges, $trialJudges);

		$tpl = new Template("car-search");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"subjects" 					 => $subjects,
			"subject"					 => $params["subject"],
			"years"						 => $years,
			"year"						 => $params["year"],
			"allMonths"					 => Date::getMonths(),
			"month"     				 => Date::getStringMonth($params["month"]),
			"allCourts" 				 => Oregon::getAppellateCourts(),
			"court"     				 => $params["court"],
			"counties"					 => Oregon::getCounties(),
			"county"					 => $params["circuit"],
			"judges"					 => $allJudges,
			"selectedAppellateJudge"     => $params["appellate_judge"],
			"selectedTrialJudge"         => $params["trial_judge"],
			"importance"				 => $params["importance"],
			"doSummarize"		 		 => $this->doSummarize,
			"selectedImportance"		 => $params["importance"]
		));

	}





}