<?php



class CaseReviewForm implements \Http\IJson {

	


	function getForm() {

			$selectsFields = ["subject_1", "plaintiff", "circut", "majority"];
			$inputs = ["title", "subject_2", "summary", "result", "defendant", "citation", "judges", "url", "full_date"];
			$selects = [];

			foreach ($selectsFields as $field) {
					$selects[$field] = MysqlDatabase::getSelectList($field, "car");
			}

			return array(
					'inputs' => $inputs,
					'selects' => $selects   
			);
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