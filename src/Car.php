<?php

class Car {

	public $id;
	public $a_number;
	public $external_link;
	public $subject_1;
	public $subject_2;
	public $summary;
	public $result;
	public $title;
	public $plaintiff;
	public $defendant;
	public $citation;
	public $month;
	public $day;
	public $year;
	public $circuit;
	public $majority;
	public $judges;
	public $url;
	public $is_flagged;
	public $is_draft;
	public $is_test;

	// This is to hold the data that will not be used as a column when inserting data.
	private $meta = array();


	public function __construct($id = null) {}

	public static function from_array_or_standard_object($record) {

		$record = (array) $record;

		$car = new Self();
		$car->id = empty($record["id"]) ? null : $record["id"];
		$car->a_number = $record["a_number"];
		$car->subject_1 = $record["subject_1"];
		$car->subject_2 = $record["subject_2"];
		$car->summary = $record["summary"];
		$car->result = $record["result"];
		$car->plaintiff = $record["plaintiff"];
		$car->defendant = $record["defendant"];

		if(!empty($record["plaintiff"]))
		$car->title = $record["title"] != null ? $record["title"] : $record["plaintiff"] . " v. " . $record["defendant"]; 
		
		$car->citation = $record["citation"];
		$car->external_link = $record["external_link"];

		if(!empty($record["date"])){

			list($car->year, $car->month, $car->day) = explode("-",$record["date"]);

		} else {

			$car->month = $record["month"];
			$car->day = $record["day"];
			$car->year = $record["year"];
		}

		$car->circuit = $record["circuit"];
		$car->majority = $record["majority"];
		$car->judges = $record["judges"];
		$car->url = $record["url"];

		$car->is_flagged = !empty($record["is_flagged"]) ? $record["is_flagged"] : "0";
		$car->is_draft = !empty($record["is_draft"]) ? $record["is_draft"] : "0";
		$car->is_test = !empty($record["is_test"]) ? $record["is_test"] : "0";

		return $car;
	}


	////////GETTERS//////////
	public function getId(){

		return $this->id;
	}

	public function getA_number() {

		return $this->a_number;
	}

	public function getSubject1(){

		return $this->subject_1;
	}

	public function getSubject2(){

		return $this->subject_2;
	}

	public function getSummary(){

		return $this->summary;
	}

	public function getResult(){

		return $this->result;
	}

	public function getTitle(){

		return $this->title;
	}

	public function getPlaintiff(){

		return $this->plaintiff;
	}

	public function getDefendant(){

		return $this->defendant;
	}

	public function getCitation(){

		return $this->citation;
	}

	public function getMonth(){

		return $this->month;
	}

	public function getDay(){

		return $this->day;
	}

	public function getYear(){

		return $this->year;
	}

	public function getCircuit(){

		return explode(" County", $this->circuit)[0];
	}

	public function getMajority(){

		return $this->majority;
	}
	
	public function getJudges(){

		return $this->judges;
	}

	public function getUrl(){

		return $this->url;
	}

	public function getDateString() {
	
		return $this->year . "-" . $this->month . "-" . $this->day;
	}
	
	
	public function getDate(){

		$dateString = $this->year . "-" . $this->month . "-" . $this->day;

		$date = new DateTime($dateString);

		$formated = $date->format("l, F jS, Y");

		return $formated;
	}

	public function isFlagged(){

		return $this->is_flagged == 1 ? true : false;
	}

	public function isDraft(){

		return $this->is_draft == 1 ? true : false;
	}

	public function isTest(){

		return $this->is_test == 1 ? true : false;
	}

	public function isNew($new = null){

		if(is_bool($new)){

			$this->meta["is_new"] = $new;
		}

		return $this->meta["is_new"];
	}

	public function getPickerCompatibleDate(){

		if(!empty($this->year)){

			$dateString = $this->year ."-". $this->month ."-". $this->day;

			$date = new \DateTime($dateString);
			$compatibleDate = $date->format("Y-m-d");

			return $compatibleDate;
		}
		//'2021-06-08' 

	}
}