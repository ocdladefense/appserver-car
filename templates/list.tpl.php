








<?php foreach($records as $record): ?>
	<?php $subject = trim($record->getSubject()); ?>
	
	<?php $group = $summarize && $subject != $previous ? $subject : ""; ?>

	<?php if(!empty($group)): ?>
		<h2 class="list-group">
			<?php print $record->getSubject(); ?>
		</h2>
	<?php endif; ?>

	<?php module_template("record", __DIR__, $record); ?>


	<?php $previous = $subject; ?>

<?php endforeach; ?>



