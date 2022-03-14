<?php
/**
 * @Template car-admin
 * 
 * Display admin-only UX functions.
 */
?>

<?php if(is_admin_user()): ?>
	<div class="admin-area">
		<a class="delete-review" data-car-id="<?php print $car->getId(); ?>" href="/car/delete/<?php print $car->getId(); ?>">
			<i style="font-size: x-large;" class="fas fa-trash-alt"></i>
		</a>
		<a class="edit-review" href="/car/edit/<?php print $car->getId(); ?>">
			<i style="font-size: x-large;" class="fas fa-edit"></i>
		</a>
		
		<!--
			<label class="checkbox-label">Flag</label>
			<input class="checkbox-option" id="car-<?php print $car->getId(); ?>" name="is_flagged" data-car-id="<?php print $car->getId(); ?>" type="checkbox" <?php print $isFlagged; ?> />
		-->
	</div> <!-- end admin area  -->
<?php endif; ?>