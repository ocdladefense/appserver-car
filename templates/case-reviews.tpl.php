<?php

define("DOM_SPACE"," ");	



?>


	<?php

		foreach($cases as $case):
			$date = $case["month"] .DOM_SPACE .$case["day"] .DOM_COMMA.DOM_SPACE.$case["year"];
			$subject = strtolower($case["subject_1"]);
			$firstHalf;
			$secondHalf;
			
		?>

		<div class="car-instance">
		
			<hr />

			<div class="row">
				<div class="car-field-container col-sm-2">
					<div class='car-date'>
						<?php print $date; ?>
					</div>
				</div>

				<div class="car-field-container col-sm-2">
					<div class='car-judges'>
						<?php print $case["judges"]; ?>
					</div>
				</div>

				<div class="car-field-container col-sm-4">
					<div class='car-title'>
						<?php print $case["title"]; ?>
					</div>
				</div>

				<div class="car-field-container col-sm-2">
					<div class='car-circut'>
						<?php print $case["circut"]; ?>
					</div>
				</div>

				<div class="car-field-container col-sm-2">
					<div class='car-citation'>
						<?php print $case["citation"]; ?>
					</div>
				</div>

				
			</div>

			<div class="row">
				<div class="car-field-container col-sm-12">
					<div class='car-subject'>
						<?php print $case["subject_1"]; ?>
					</div>
				</div>
			</div>

			<div class="car-field-container">
				<div class='car-subject'>
					<?php print strtoupper($case["subject_2"]); ?>
				</div>
			</div>
			
			<div class="car-field-container">
				<div class='car-summary'>
					<?php 

						//$resultPos = strpos($case["summary"],$case["result"]);

						if(strlen($case["summary"]) > 350){
							$firstPart = substr($case["summary"], 0, 350);
							$secondPart = substr($case["summary"], 350);

							print $firstPart;
					?>
							<span class="ellipsis">...</span>

							<span class="more"><?php print $secondPart ?></span>

							<button class="btn btn-link readMoreButton">Read More</button>

						<?php
						}
						else{
							print $case["summary"];
						}
					 	 ?>
				</div>
				

				
			</div>

		</div>
	<?php endforeach; ?>
	