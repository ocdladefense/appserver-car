<?php

define("DOM_SPACE"," ");	



?>
<script> 
const options = <?php print($subjectJson) ?>;
</script>
<!-- <input placeholder="For demonstrating event framework" type="text" style="width: 400px; position: relative; top: -140px;"></input> -->

<div id="car-results">
	<?php
		foreach($cases as $case):
			$date = $case["month"] .DOM_SPACE .$case["day"] .DOM_COMMA.DOM_SPACE.$case["year"];
			$subject = strtolower($case["subject_1"]);

		?>

		<div class="car-instance">
		
			<hr />

			<div class="row">
				<div id="date" class="car-field-container col-2">
					<div class='car-date'>
						<?php print $date; ?>
					</div>
				</div>

				<div id="judges" class="car-field-container col-2">
					<div class='car-judges'>
						<?php print $case["judges"]; ?>
					</div>
				</div>

				<div id="title" class="car-field-container col-4">
					<div class='car-title'>
						<?php print $case["title"]; ?>
					</div>
				</div>

				<div id="circut" class="car-field-container col-2">
					<div class='car-circut'>
						<?php print $case["circut"]; ?>
					</div>
				</div>

				<div id="citation" class="car-field-container col-2">
					<div class='car-citation'>
						<?php print $case["citation"]; ?>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="car-field-container col-12">
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

						if($case['useTeaser']):				
							print $case['teaser'];
												
					?>
							<span class="ellipsis">...</span>

							<span class="more"><?php print $case['readMore'] ?></span>

							<button class="btn btn-link readMoreButton">Read More</button>

						<?php
						
						else:
							print $case["summary"];
						endif;
					 	 ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
	