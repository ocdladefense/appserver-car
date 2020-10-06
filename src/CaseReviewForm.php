<?php



class CaseReviewForm implements \Http\IJson {

	


	function getForm() {

		$fieldOrder = ["title", "full_date", "subject_1", "subject_2", "summary", "result", "plaintiff",
			"defendant", "citation", "circut", "majority", "judges", "url", "day", "month", "year"];

		$allFields = [];

		foreach ($fieldOrder as $field) {
			$fieldObj = new stdClass();
			$fieldObj->field = $field;
			$fieldObj->label = $this->formatLabel($field) . ":";

			if (in_array($field, ["subject_1", "plaintiff", "circut", "majority"])) {
				$fieldObj->type = "lookup";
				$fieldObj->values = MysqlDatabase::getSelectList($field, "car");
			} else if (in_array($field, ["summary", "result"])) {
				$fieldObj->type = "textinput-textarea";
				$input = new stdClass();
				$input->rows = 5;
				$props = new stdClass();
				$props->input = $input;
				$fieldObj->props = $props;
				//$fieldObj->props->input->rows = 5;
			} else if (in_array($field, ["day", "month", "year"])) {
				$fieldObj->type = "hidden";
			} else {
				$fieldObj->type = "textinput";
				if ($field == "full_date") {
					$input = new stdClass();
					$input->type = "date";
					$props = new stdClass();
					$props->input = $input;
					$fieldObj->props = $props;
					//$fieldObj->props->input->type = "date";
				}
			}
			$allFields[] = $fieldObj;
		}

		return array(
				'allFields' => $allFields   
		);
	}

	function formatLabel($field) {
		switch($field) {
			case "circut":
				return "circuit";
			case "subject_1":
				return "subject 1";
			case "subject_2":
				return "subject 2";
			case "full_date":
				return "date";
			default:
				return $field;
		}
	}
	
	function toJson() {
		return json_encode($this->getForm());
	}




	
	/**
	 * @todo - May not be necessary since we are building forms in clientside scripts.
	 */
	private function attachTemplateFiles() {
			$carDir = dirname(__DIR__, 1);
			Template::addPath($carDir . "/templates");

			$template = Template::loadTemplate("webconsole");

			$css = array(
					"active" => true,
					"href" => "/modules/car/css/carCreateStyles.css"
			);
		
			$template->addStyle($css);

			$js = array(
					array(
							"src" => "/modules/car/src/FormSubmission.js"
					),
					array(
							"src" => "/modules/car/src/FormParser.js"
					),
					array(
							"src" => "/modules/car/src/DBQuery.js"
					),
					array(
							"src" => "/modules/car/src/BaseComponent.js"
					),
					array(
							"src" => "/modules/car/src/CreateCarUI.js"
					),
					array(
							"src" => "/modules/car/src/CarCreateModule.js"
					)
			);

			$template->addScripts($js);

			return $template;
	}
	
}