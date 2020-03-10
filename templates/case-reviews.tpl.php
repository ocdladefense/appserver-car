<?php

define("DOM_SPACE"," ");




?>


	<?php

		foreach($cases as $case):
			$date = $case["month"] .DOM_SPACE .$case["day"] .DOM_COMMA.DOM_SPACE.$case["year"];
			$subject = strtolower($case["subject_1"]);
			
		?>
		<div class="car-instance">
		
			<div class="car-field-container">
				<div class='car-date'></div>
			</div>
			
			<div class="car-field-container">
				<div class='car-title'>
					<?php print $case["title"]; ?>
				</div>
			</div>
			
			<div class="car-field-container">
				<div class='car-subject'>
					<?php print $case["subject_1"]; ?>
				</div>
			</div>

			<div class="car-field-container">
				<?php print $case["summary"]; ?>
			</div>

		</div>
		
	<?php endforeach; ?>
	