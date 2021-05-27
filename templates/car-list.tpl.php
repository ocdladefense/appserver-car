
<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/carlist.css" />


<div id="car-list-container" class="list-containter">

    <h1 class="list-header">Library of Defense Case Review Records</h1><br /><br />

    <?php if(!empty($cars)) : ?>
        <?php foreach($cars as $car) : ?>

            <div id="car-container" class="car-container">
                <label><strong>Title: </strong><?php print $car->getTitle(); ?></label><br />
                <label><strong>Decision Date: </strong><?php print $car->getDate(); ?></label><br />
                <label><strong>Citation: </strong><?php print $car->getCitation(); ?></label><br />
                <label><strong>Primary Subject: </strong><?php print $car->getSubject1(); ?></label><br />
                <label><strong>Secondary Subject: </strong><?php print $car->getSubject2(); ?></label><br />
                <label><strong>Curcuit: </strong><?php print $car->getCircuit(); ?></label><br />
                <label><strong>Majority: </strong><?php print $car->getMajority(); ?></label><br />
                <label><strong>Judges: </strong><?php print $car->getJudges(); ?></label><br /><br />
                <label><strong>Summary: </strong><?php print $car->getSummary(); ?></label><br /><br />
                <label><strong>Result: </strong><?php print $car->getResult(); ?></label><br />
            </div>

        <?php endforeach; ?>
    <?php else : ?>

        <div class="car-container" style="text-align: center;">
            <h1>No case reviews were found.</h1>
        </div>

    <?php endif; ?>

</div> <!-- end list container -->

