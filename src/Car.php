<?php

class Car {

	private $id;
	private $subject_1;
	private $subject_2;
	private $summary;
	private $result;
	private $title;
	private $plaintiff;
	private $defendant;
	private $citation;
	private $month;
	private $day;
	private $year;
	private $circuit;
	private $majority;
	private $judges;
	private $url;


	public function __construct($id = null) {}

	public static function from_query_result_record($record) {

		$car = new Self();
		$car->id = $record["id"];
		$car->subject_1 = $record["subject_1"];
		$car->subject_2 = $record["subject_2"];
		$car->summary = $record["summary"];
		$car->result = $record["result"];
		$car->title = $record["title"];
		$car->plaintiff = $record["plaintiff"];
		$car->defendant = $record["defendant"];
		$car->citation = $record["citation"];
		$car->month = $record["month"];
		$car->day = $record["day"];
		$car->year = $record["year"];
		$car->circuit = $record["circuit"];
		$car->majority = $record["majority"];
		$car->judges = $record["judges"];
		$car->url = $record["url"];


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

	public function getDate(){

		$dateString = $this->year . "-" . $this->month . "-" . $this->day;

		$date = new DateTime($dateString);

		$formated = $date->format("l, F jS, Y");

		return $formated;
	}
}