<?php


if($subjectJson == "") {
	$subjectJson = "[]";
}
?>


<?php if(count($cases) <= 0): ?> 
	<h4 style="text-align: center;">There are no results that match your search.</h4>
<?php exit; endif; ?>



<script> 
const subjects = <?php print($subjectJson) ?>;
const dateRanges = <?php print($dateRangesJson) ?>;
const searches = <?php print($searchesJson) ?>;
const sorts = <?php print($sortsJson) ?>;
const loadLimit = <?php print($loadLimit) ?>;
const loadOffset = <?php print($loadOffset) ?>;
</script>


<!-- <input placeholder="For demonstrating event framework" type="text" style="width: 400px; position: relative; top: -140px;"></input> -->

<div id="car-results">
	<?php
		foreach($cases as $case):
			$date = $case["month"] .DOM_SPACE .$case["day"] .DOM_COMMA.DOM_SPACE.$case["year"];
			$subject = strtolower($case["subject_1"]);

			$containerId = "car-container-".$case["id"];
		?>

		<div class="car-instance" id="<?php print $containerId ?>">
			<div id="car-id">
				<?php print $case["id"]; ?>
			</div>
			<hr />
			<div id="title" class="car-field-container">
					<div class='car-title'>
						<?php print $case["title"]; ?>
					</div>
				</div>

				<div id="date" class="car-field-container">
					<div class='car-date'>
						<?php print $date; ?>
					</div>
				</div>

				<div id="judges" class="car-field-container">
					<div class='car-judges'>
						<?php print $case["judges"]; ?>
					</div>
				</div>

				<div id="circut" class="car-field-container">
					<div class='car-circut'>
						<?php print $case["circut"]; ?>
					</div>
				</div>

				<div id="citation" class="car-field-container">
					<div class='car-citation'>
						<?php print $case["citation"]; ?>
					</div>
				</div>

				<div class="car-field-container">
					<div class='car-subject'>
						<?php print strtoupper($case["subject_1"]); ?>
					</div>
				</div>

			<div class="car-field-container">
				<div class='car-subject-2'>
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
			<a class="car-link-btn car-update-link" data-carId="<?php print $case["id"]; ?>" href="#">
				<span>Update</span>
			</a>
			<a class="car-link-btn car-delete-link" data-carId="<?php print $case["id"]; ?>" href="#">
				<span>Delete</span>
			</a>
		</div>


	<?php endforeach; ?>
</div>
	