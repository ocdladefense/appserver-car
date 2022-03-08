



<?php if(empty($cars)): ?>
    
	<div class="car-container" style="text-align: center;">
		<h1>No case reviews were found.</h1>
	</div>

<?php endif; ?>




<?php foreach($cars as $car) {
	$grouping = $groupBy == "subject" && $subject != $car->getSubject() ? $car->getSubject() : "";
?>
	<?php if($grouping): ?>
		<h2 class="subject-header <?php print $isFirstClass; ?>">
			<?php print $car->getSubject(); ?>
		</h2>
	<?php endif; ?>


	<?php module_template("car", $car); ?>


<?php $subject = $car->getSubject();
} ?>



