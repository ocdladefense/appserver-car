<?php

class Car {

	public $id;
	public $importance;
	public $title;
	public $court;
	public $plaintiff;
	public $defendant;
	public $trial_judge;
	public $appellate_judge;
	public $subject;
	public $secondary_subject;
	public $summary;
	public $a_number;
	public $citation;
	public $circuit;
	public $month;
	public $day;
	public $year;
	public $external_link;
	public $url;
	public $is_draft;
	public $is_flagged;


	// This is to hold the data that will not be used as a column when inserting data.
	private $meta = array();


	public function __construct($id = null) {}

	public static function from_array_or_standard_object($record) {

		$record = (array) $record;

		$car = new Self();
		$car->id = empty($record["id"]) ? null : $record["id"];
		$car->importance = $record["importance"];
		$car->plaintiff = $record["plaintiff"];
		$car->defendant = $record["defendant"];

		if(!empty($record["plaintiff"])) {
			
			$car->title = $record["title"] != null ? $record["title"] : $record["plaintiff"] . " v. " . $record["defendant"];
		}

		$car->court = $record["court"];
		$car->trial_judge = $record["trial_judge"];
		$car->appellate_judge = $record["appellate_judge"];
		$car->subject = $record["subject"];
		$car->secondary_subject = $record["secondary_subject"];
		$car->summary = $record["summary"];
		$car->a_number = $record["a_number"];
		$car->citation = $record["citation"];
		$car->circuit = $record["circuit"];
		$car->external_link = $record["external_link"];
		$car->is_flagged = !empty($record["is_flagged"]) ? $record["is_flagged"] : "0";
		$car->is_draft = !empty($record["is_draft"]) ? $record["is_draft"] : "0";
		$car->url = $record["url"];

		if(!empty($record["date"])){

			list($car->year, $car->month, $car->day) = explode("-",$record["date"]);

		} else {

			$car->month = $record["month"];
			$car->day = $record["day"];
			$car->year = $record["year"];
		}

		return $car;
	}


	////////GETTERS//////////
	public function getId(){

		return $this->id;
	}

	public function getImportance() {

		return $this->importance;
	}

	public function getA_number() {

		return $this->a_number;
	}

	public function getSubject1(){

		return $this->subject;
	}

	public function getSubject2(){

		return $this->secondary_subject;
	}

	public function getSummary(){

		return $this->summary;
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

	public function getExternalLink(){

		return $this->external_link;
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

	public function getAppellateJudge(){

		return $this->appellate_judge;
	}
	
	public function getTrialJudge(){

		return $this->trial_judge;
	}

	public function getUrl(){

		return $this->url;
	}

	public function getDateString() {
	
		return $this->year . "-" . $this->month . "-" . $this->day;
	}
	
	
	public function getDate(Bool $showWeekDay = true){

		$dateString = $this->year . "-" . $this->month . "-" . $this->day;

		$date = new DateTime($dateString);

		$format = $showWeekDay ? "l, F jS, Y" : "F jS, Y";

		$formated = $date->format($format);

		return $formated;
	}

	public function getCourt(){

		return $this->court;
	}

	public function isFlagged(){

		return $this->is_flagged == 1 ? true : false;
	}

	public function isDraft(){

		return $this->is_draft == 1 ? true : false;
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