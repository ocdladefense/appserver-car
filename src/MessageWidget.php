<?php



class MessageWidget extends Component {


		

	public function getUserFriendlyMessages($params, $cars, $query){

		$tpl = new Template("car-message");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
			"message"      => $this->getUserMessage($params, count($cars)),
			"user"		   => get_current_user(),
			"query"        => $query
		));
	}




	public function getUserMessage($params, $count){

		$year = $params["year"];
		$month = $this->getStringMonth($params["month"]);
		$day = $params["day"];
		$court = $params["court"];
		$subject = $params["subject"];
		$county = $params["circuit"];

		$courtMsg = empty($court) ? "" : "in $court";

		$month = $month == "All Months" ? null : $month;

		if(!empty($month)) $dateMsg = empty($year) ? "for the month of $month (All Years)" : "for $month";
		if(!empty($day)) $dateMsg .= ", $day";
		if(!empty($year)) $dateMsg .= empty($month) ? "for $year" : ", $year";

		$msg = "";

		if(!empty($subject)) $msg .= "<h3>$subject</h3>";

		$msg .= "showing " . $count . " case review(s)";
		if(!empty($courtMsg)) $msg .= " $courtMsg";
		if(!empty($dateMsg)) $msg .= " $dateMsg";

		if(!empty($county)) $msg .= "<h4>$count decision(s) made in $county County</h4>";


		return $msg;
	}


}