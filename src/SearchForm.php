<?php

class SearchForm implements IRenderable {

	private $context = array();
	
	
	public function getContext() {
		return $this->context;
	}
	
	
	public function __construct() {

		$loadLimit = 10;
		
		$subjects = $this->getListOptions("subject_1");
		$defaultSubject = new stdClass();
		$defaultSubject->name = "All Subjects";
		$defaultSubject->value = "";
		$subjectSettings = new stdClass();
		$subjectSettings->field = "subject_1";
		$subjectSettings->options = $subjects;
		$subjectJson = empty($subjects) ? json_encode(array($defaultSubject)) : json_encode($subjectSettings);
		//$subjectJson = "";
		$config12 = array(
			"numOfMonths" => 12, "inclusive" => false
		);
		$config6 = array(
			"numOfMonths" => 6, "inclusive" => false
		);
		$dateRanges = array(
			["--ALL-- (Select Date Range)", "ALL"],
			["Last Year", $this->calculateDays($config12)], 
			["Last 6 Months", $this->calculateDays($config6)], 
			["Last 30 Days", 30],
			["----------------", "space"], 
			["This Year", $this->thisYear()], 
			["This Month", $this->thisMonth()]
		);
		$dateSettings = new stdClass();
		$dateSettings->field = "datediff(curdate(), full_date)";
		$dateSettings->op = "<";
		$parsedDates = array();
		foreach($dateRanges as $dateRange) {
			$option = new stdClass();
			$option->name = $dateRange[0];
			$option->value = $dateRange[1];
			$parsedDates[] = $option;
		}
		$dateSettings->options = $parsedDates;
		$dateRangesJson = json_encode($dateSettings);
		//Nested arrays describe the field to be ordered by and if it should order by desc
		$sorts = array(
			["Newest to Oldest ", ["full_date", true]],
			["Oldest to Newest", ["full_date", false]],
			["Title Alphabetically", ["title", false]]
		);
		$parsedSorts = array();

		foreach($sorts as $sort) {
			$option = new stdClass();
			$option->name = $sort[0];
			$option->value = $sort[1][0];
			$option->desc = $sort[1][1];
			$parsedSorts[] = $option;
		}
		$sortsJson = json_encode($parsedSorts);
 
		$searches = array("summary", "title");
 
		$parsedSearches = array();
		foreach($searches as $search) {
			$option = new stdClass();
			$option->name = $search;
			$option->value = $search;
			$parsedSearches[] = $option;
		}
		$searchesJson = json_encode($parsedSearches);
		
		
		$this->context = array(
			'subjectJson' 			=> $subjectJson,
			'dateRangesJson'		=> $dateRangesJson,
			'searchesJson'			=> $searchesJson,
			'sortsJson'					=> $sortsJson,
			'loadLimit'					=> $loadLimit,
			'loadOffset'				=> 0
		);
	}
	
	
	


	private function getListOptions($field) {
		$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
		$parsedResults = array();
		foreach($dbResults as $result) {
			$option = new stdClass();
			$option->name = $result[$field];
			$option->value = $result[$field];
			$parsedResults[] = $option;
		}
		return $parsedResults;
	}

	public function getSelectList($field) {
		$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
		$parsedResults = array();
		foreach($dbResults as $result) {
			$parsedResults[] = $result[$field];
		}
		return json_encode($parsedResults);
	}
	
	private function calculateDays($config) {
		$numOfMonths = $config["numOfMonths"];
		$inclusive = $config["inclusive"];

		$month = date("m") - $numOfMonths;
		$month = $month <= 0 ? 12 + $month : $month;

		$numOfYears = floor($numOfMonths / 12);
		
		$day = date("d");
		$year = date("Y") - $numOfYears;

		$d=mktime(0, 0, 0, $month, $day, $year);
		$days = floor((time() - $d)/60/60/24);

		if ($inclusive) $days += 1;

		return $days;
	}
	

	private function thisMonth() {
		return date("d");
	}

	private function thisYear() {
		$year = date("Y");
		$daysInYear = 0;

		for ($month = 1; $month < date("m"); $month++) {
			$daysInYear += cal_days_in_month(CAL_GREGORIAN, $month, $year);
		}

		$daysInYear += date("d");

		return $daysInYear;
	}
	
}


