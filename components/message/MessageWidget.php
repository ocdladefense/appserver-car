<?php



class MessageWidget extends Presentation\Component {






	public function __construct($name, $id, $params = array()) {

		parent::__construct($name, $id, $params);
		$this->template = "car-message";

		$this->court = "Oregon Supreme Court";
		// $input = $this->getInput();
		
		// Assign to instance variables.
		// Those variables will be available as $this->year, etc
		// in the template file.
		$year = $params["year"];
		$month = $params["month"];
		$day = $params["day"];
		$court = $params["court"];
		$subject = $params["subject"];
		$county = $params["circuit"];
		// $user, etc.
	}	




	// This shouldn't exist.  Don't do this: all HTML should be produced in template files.
	/* public function getUserMessage($params, $count){

		return $msg;
	}
	*/

}