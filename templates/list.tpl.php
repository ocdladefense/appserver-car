



<?php if(empty($records)): ?>
    
	<div class="car-container" style="text-align: center;">
		<h1>No case reviews were found.</h1>
	</div>

<?php endif; ?>




<?php foreach($records as $record) {
	$grouping = $groupBy == "subject" && $subject != $record->getSubject() ? $record->getSubject() : "";
?>
	<?php if($grouping): ?>
		<h2 class="subject-header <?php print $isFirstClass; ?>">
			<?php print $record->getSubject(); ?>
		</h2>
	<?php endif; ?>

	<?php module_template("record", __DIR__, $record); ?>


<?php $subject = $record->getSubject();
} ?>



