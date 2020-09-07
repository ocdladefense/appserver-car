<?php

class CaseReviewsTemplate extends Template {

		// Component styles.
		private $css = array(
			array(
				"active" => true,
				"href" => "/modules/car/assets/css/styles.css",
			),
			array(
				"active" => true,
				"href" => "/modules/car/assets/css/carCreateStyles.css"
			),
			array(
				"active" => true,
				"href" => "/content/libraries/view/loading.css"
			)
		);
		

		// Component scripts.
		private $core = array(
			
			"/content/libraries/database/DBQuery.js",
			"/content/libraries/component/BaseComponent.js",
			"/content/libraries/form/FormParser.js",
			"/content/libraries/form/FormSubmission.js",
			"/content/libraries/view/view.js",
			"/content/libraries/view/modal.js"
		);

		/*
		<script src="/modules/car/assets/js/InfiniteScroller.js"></script>
		<script src="/modules/car/assets/js/PageUI.js"></script>
		<script src="/modules/car/assets/js/module.js"></script>
		<!--<script src="/modules/car/assets/js/CreateCarUI.js"></script>
		<script src="/modules/car/assets/js/CarCreateModule.js"></script>-->
		*/
		private $module = array(
			"InfiniteScroller.js", // maybe
			
			// all custom below here.
			"PageUI.js",
			"CreateCarUI.js",
			//"CarCreateModule.js",
			"module.js"
		);



		public function __construct() {
			parent::__construct("case-reviews");
			
			$this->addStyles($this->css);

			$scripts = array();
			
			foreach($this->core as $name) {
				$scripts [] = array("src" => $name);			
			}
			foreach($this->module as $name) {
				$scripts [] = array("src" => "/modules/car/assets/js/".$name);			
			}
			
			
			$this->addScripts($scripts);
		}
	

		
		public function formatResults($results, $config) {
			
			// Number of words to display in the teaser
			$teaserWordLength = $config['teaserWordLength'];
			
			// Minimun number of characters
			$teaserCutoff = $config['teaserCutoff'];
			
			// Whether to use teasers, or not.
			$useTeasers = $config['useTeasers'];
		
			$cases = [];

			//try {			
				foreach($results as $result) {

					$case = $result;

					$case["month"] = substr($case["month"], 0, 3);
					$case["month"] .= ".";

					$summaryArray =  explode(" " , $case["summary"]);
					$case['useTeaser'] = $useTeasers === true && strlen($case["summary"]) > $teaserCutoff;

					$case['teaser'] = implode(" ", array_slice($summaryArray, 0, $teaserWordLength));
					$case['readMore'] = implode(" ", array_slice($summaryArray, $teaserWordLength));

					$cases[] = $case;
				} 
			//} catch(Exception $e) {
				//return $this->bind("cases", $results);
			//}
			return $this->bind("cases",$cases);
		}
}