<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-list.css" />


<div id="car-list-container" class="list-containter">

    <h1 class="list-header">OCDLA Criminal Appellate Review Summaries</h1>

    <div class="car-container">

       <?php print $searchForm; ?>

    </div>

    <?php if(empty($cars)) { ?>
    

        <div class="car-container" style="text-align: center;">
            <h1>No case reviews were found.</h1>
        </div>
        

    <?php } ?>
    

	<?php foreach($cars as $car) {
				
		$isFlagged = $car->isFlagged() ? "checked" : "";

		$classesArray = array();

		if($car->isNew()) $classesArray[] = "is-new";

		$classes = implode(" ", $classesArray);

		$previousSubject = $subject;
		$subject = $car->getSubject1();

		$newSubject = $previousSubject != $subject;
		
		
		?>

		<?php if($newSubject && $groupBy == "subject_1") : ?>
			<h2><?php print $subject; ?></h2>
		<?php endif; ?>

		<div class="car-container <?php print $classes; ?>">
		
				
			<?php if($user->isAdmin()) : ?>
				<div class="admin-area">
					<a class="delete-review" data-car-id="<?php print $car->getId(); ?>" href="/car/delete/<?php print $car->getId(); ?>"><i style="font-size: x-large;" class="fas fa-trash-alt"></i></a>
					<a class="edit-review" href="/car/edit/<?php print $car->getId(); ?>"><i style="font-size: x-large;" class="fas fa-edit"></i></a>
					<label class="checkbox-label">Flag</label>
					<input class="checkbox-option" id="car-<?php print $car->getId(); ?>" name="is_flagged" data-car-id="<?php print $car->getId(); ?>" type="checkbox" <?php print $isFlagged; ?> />
				</div> <!-- end admin area  -->
			<?php endif; ?>
				
			<div class="car-item title">
				<?php print $car->getTitle(); ?>
			</div>
			
			<div class="car-item decision-date">
				<?php print $car->getDate(); ?>
			</div>

			<div class="car-item subject-1">
				<?php print $car->getSubject1(); ?>
			</div>
			
			<div class="car-item subject-2">
				<?php print $car->getSubject2(); ?>
			</div>

			<label>
				<strong>Summary:</strong>
				<?php print $car->getSummary(); ?>
			</label>

			<label>
				<strong>Result:</strong>
				<?php print $car->getResult(); ?>
			</label>

			<div class="additional-info">

				<label>
					<strong>Citation: </strong>
					<?php print $car->getCitation(); ?>
				</label>
				
				<label>
					<strong>Circuit:</strong>
					<?php print $car->getCircuit(); ?>
				</label>
				
				<label>
					<strong>Majority:</strong>
					<?php print $car->getMajority(); ?>
				</label>
				
				<label>
					<strong>Judges:</strong>
					<?php print $car->getJudges(); ?>
				</label>
			</div>
			
			<?php if($car->getUrl() != null && $user->isAdmin()) : ?>
				<a href="<?php print $car->getUrl(); ?>" target="_blank">View on the Library of Defense website</a>
			<?php endif; ?>
		</div>

	<?php } ?>



</div> <!-- end list container -->


<script src="<?php print module_path(); ?>/assets/js/car-flag.js"></script>
<script src="<?php print module_path(); ?>/assets/js/car.js"></script>

