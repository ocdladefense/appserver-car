<?php




class SearchWidget extends Component {


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
			"month"     				 => $this->getStringMonth($params["month"]),
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