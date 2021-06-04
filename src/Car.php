<?php

class Car {

	public $id;
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


	public function __construct($id = null) {}

	public static function from_array_or_standard_object($record) {

		$record = (array) $record;

		$car = new Self();
		$car->id = empty($record["id"]) ? null : $record["id"];
		$car->subject_1 = $record["subject_1"];
		$car->subject_2 = $record["subject_2"];
		$car->summary = $record["summary"];
		$car->result = $record["result"];
		$car->plaintiff = $record["plaintiff"];
		$car->defendant = $record["defendant"];
		$car->title = $record["title"] != null ? $record["title"] : $record["plaintiff"] . " v. " . $record["defendant"]; 
		$car->citation = $record["citation"];
		$car->month = $record["month"];
		$car->day = $record["day"];
		$car->year = $record["year"];
		$car->circuit = $record["circuit"];
		$car->majority = $record["majority"];
		$car->judges = $record["judges"];
		$car->url = $record["url"];
		$car->is_flagged = $record["is_flagged"];

		$car->is_draft = $record["is_draft"];
		$car->is_test = $record["is_test"];

		return $car;
	}


	////////GETTERS//////////
	public function getId(){

		return $this->id;
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

		return $this->circuit;
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
}