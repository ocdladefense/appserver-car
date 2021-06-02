


<div id="car-list-container" class="list-containter">

    <h1 class="list-header">Library of Defense Case Review Records</h1>

    <div class="car-container">

        <form id="filter-form" action="/car/list" method="post">
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
        </form>

    </div>

    <?php if(false && empty($cars)) { ?>
    

        <div class="car-container" style="text-align: center;">
            <h1>No case reviews were found.</h1>
        </div>
        

    <?php } ?>
    

		<?php 
		
			
		
		foreach($cars as $car) {
				
				$checked = $car->isFlagged() ? "checked" : "";
				
				
				?>

				<div id="car-container" class="car-container">
				
				
				
						<div id="logo" class="logo" style="float:right;">
								<a href="//www.ocdla.org">
									<img src="/content/images/logo.png" />
								</a>
								<br />
								<label>Flag this review</label>
								
								<input class="flag-review" id="car-<?php print $car->getId(); ?>" data-car-id="<?php print $car->getId(); ?>" type="checkbox" <?php print $checked; ?> name="flagged" />
						</div>
						
						<label>
							<strong>Title:</strong>
							<?php print $car->getTitle(); ?>
						</label>
						
						<label>
							<strong>Decision Date:</strong>
							<?php print $car->getDate(); ?>
						</label>
						
						<label>
							<strong>Citation: </strong>
							<?php print $car->getCitation(); ?>
						</label>
						
						<label>
							<strong>Primary Subject:</strong>
							<?php print $car->getSubject1(); ?>
						</label>
						
						<label>
							<strong>Secondary Subject:</strong>
							<?php print $car->getSubject2(); ?>
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
						
						<label>
							<strong>Summary:</strong>
							<?php print $car->getSummary(); ?>
						</label>
						
						<label>
							<strong>Result:</strong>
							<?php print $car->getResult(); ?>
						</label>
						
						<a href="<?php print $car->getUrl(); ?>" target="_blank">View on the Library of Defense website</a>
				</div>

		<?php } ?>



</div> <!-- end list container -->

<script>
    function submitForm(){

        document.getElementById("filter-form").submit();
    }
</script>


<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/carlist.css" />
<script src="<?php print module_path(); ?>/assets/js/carFlag.js"></script>

