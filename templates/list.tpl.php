



<?php if(empty($records)): ?>
    
	<div class="car-container" style="text-align: center;">
		<h1>No case reviews were found.</h1>
	</div>

<?php endif; ?>




<?php foreach($records as $record) {
	$group = $summarize && $subject != trim($record->getSubject()) ? $record->getSubject() : "";
?>
	<?php if(!empty($group)): ?>
		<h2 class="list-group">
			<?php print $record->getSubject(); ?>
		</h2>
	<?php endif; ?>

	<?php module_template("record", __DIR__, $record); ?>


<?php $subject = trim($record->getSubject());
} ?>



