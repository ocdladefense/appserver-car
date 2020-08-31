<?php

class CaseReviewsTemplate extends Template {

		// Component styles.
		private $css = array(
			"active" => true,
			"href" => "/modules/car/assets/css/styles.css",
		);
		

		// Component scripts.
		private $js = array(
			// "/modules/car/src/settings.js",
			"BaseComponent.js",
			"FormParser.js",
			"FormSubmission.js",
			"DBQuery.js",
			// "EventFramework.js",
			"car.js",
			"InfiniteScroller.js",
			"module.js",
			"PageUI.js"
		);



		public function __construct() {
			parent::__construct("case-reviews");
			
			$this->addStyles($this->css);

			
			$js = array_map(function($name) {
				return array("/modules/car/assets/js/" . $name);
			}, $this->js);
			
			
			$this->addScripts($js);
		}
	

		
		public function formatResults($results, $config) {
			
			// Number of words to display in the teaser
			$teaserWordLength = $config['teaserWordLength'];
			
			// Minimun number of characters
			$teaserCutoff = $config['teaserCutoff'];
			
			// Whether to use teasers, or not.
			$useTeasers = $config['useTeasers'];
		
			$cases = [];



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


			return $this->bind("cases",$cases);
		}
}