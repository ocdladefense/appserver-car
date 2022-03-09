<?php



class MessageWidget extends Presentation\Component {

	public $query;



	public function __construct($name, $id, $params = array()) {

		parent::__construct($name, $id, $params);
	
		$this->template = "message";

		
		$input = $this->getInput();
	}	


	public function getStyles() {
		return array(
			"href" => module_path() . "/car/components/message/main.css",
			"active" => true
		);
	}


}