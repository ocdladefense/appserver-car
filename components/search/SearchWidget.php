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

	public $court;

	public $county;

	public $subject;

	public $date;

	public $appellate_judge;

	public $trial_judge;

	public $rank;

	public $summarize;


	public function __construct($name) {
		
		parent::__construct($name);
		$this->template = "search";

		$input = $this->getInput();

		$this->subject = $input->subject;
		$this->county = $input->county;
		$this->court = $input->court;
		$this->appellate_judge = $input->appellate_judge;
		$this->trial_judge = $input->trial_judge;
		$this->rank = $input->importance;
		$this->min_date = "2018-01-01";
		$this->summarize = $input->summarize;
	}


    public function getStyles() {
        return array(
            "active" => true,
            "href" => module_path() . "/components/search/main.css?bust=001"
        );
    }

    public function getScripts() {
        return array(
            "src" => module_path() . "/components/search/main.js?bust=001"
        );
    }


	public function getSubjects() {


		$subjects = array();
		$distinct = DbHelper::getDistinctFieldValues("car", "subject");

		array_walk($distinct, function($value) use(&$subjects) {
			$subjects[$value] = ucwords($value);
		});
	
		$default = array("" => "All Subjects");
		$all = $default + $subjects;

		// var_dump($allSubjects);exit;

		return $all;
	}


	public function getDate() {


		$yearDefault = array("" => "All Years");
		$allYears = $yearDefault + $years;
	}


	public function getCounties() {


		$loaded = Oregon::getCounties();

		$default = array("" => "All Counties");
		$counties = $default + $loaded;

		return $counties;
	}


	public function getCourts() {


		$loaded = Oregon::getCourts();

		$default = array("" => "All Courts");
		$courts = $default + $loaded;

		return $courts;
	}


	
	public function getJudges() {

		$appellateJudges = DbHelper::getDistinctFieldValues("car", "appellate_judge");
		$trialJudges = DbHelper::getDistinctFieldValues("car", "trial_judge");

		return array_merge($appellateJudges, $trialJudges);
	}



	public function getRanks() {
		return array(
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


}