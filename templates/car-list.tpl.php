<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-list.css" />


<div id="car-list-container" class="list-containter">

    <h1 class="list-header">Library of Defense Case Review Records</h1>

    <div class="car-container">

        <form id="filter-form" class="filter-form" action="/car/list" method="post">
            <label><strong>Filter:</strong></label>

            <select id="subjects" name="filter" style="text-align:center;" onchange="submitForm()">
            
                <option value="<?php print $filter != null ? $filter : ""; ?>">
                <?php 	print $filter != null ? $filter : "SHOW ALL"; ?>
                </option>
                
                <?php if($filter != null) : ?>
					<option value="">SHOW ALL</option>
                <?php endif; ?>
                
                <?php foreach($subjects as $subject) : ?>
					<option value="<?php print $subject; ?>">
						<?php print $subject; ?>
					</option>
                <?php endforeach; ?>
                
            </select>

            <label><strong><?php print "Showing " . count($cars) . " case review(s)."; ?></strong></label>
			<?php if($isAdmin) : ?><a class="add-review" href="/car/new">Add Case Reviews</a><?php endif; ?>

        </form>

    </div>

    <?php if(false && empty($cars)) { ?>
    

        <div class="car-container" style="text-align: center;">
            <h1>No case reviews were found.</h1>
        </div>
        

    <?php } ?>
    

		<?php 
		
			
		
		foreach($cars as $car) {
				
				$isFlagged = $car->isFlagged() ? "checked" : "";
				$isNewClass = $car->isNew() ? "is-new" : "";
				$isTestClass = $car->isTest() ? "is-test" : "";
				$isDraftClass = $car->isDraft() ? "is-draft" : "";

				$classesArray = array($isNewClass, $isTestClass, $isDraftClass);

				$classes = implode(" ", $classesArray);
				
				
				?>

				<div class="car-container <?php print $classes; ?>">
				
						
					<?php if($isAdmin) : ?>
						<div class="admin-area">
							<a class="delete-review" href="/car/delete/<?php print $car->getId(); ?>">Delete</a>
							<a class="edit-review" href="/car/edit/<?php print $car->getId(); ?>">Edit</a>
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
					
					<?php if($car->getUrl() != null) : ?>
						<a href="<?php print $car->getUrl(); ?>" target="_blank">View on the Library of Defense website</a>
					<?php endif; ?>
				</div>

		<?php } ?>



</div> <!-- end list container -->

<script>
    function submitForm(){

        document.getElementById("filter-form").submit();
    }
</script>



<script src="<?php print module_path(); ?>/assets/js/car-flag.js"></script>

