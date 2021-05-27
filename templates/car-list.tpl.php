
<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/carlist.css" />


<div id="car-list-container" class="list-containter">

    <h1 class="list-header">Library of Defense Case Review Records</h1><br /><br />

    <div class="car-container">
        <form id="filter-form" action="/car/list" method="post">
            <label><strong>Filter:</strong></label>
            <select id="subjects" name="filter" style="text-align:center;" onchange="submitForm()">
                    <option value="<?php print $filter != null ? $filter : "SHOW ALL"; ?>"><?php print $filter != null ? $filter : "SHOW ALL"; ?></option>
                    <?php if($filter != null) : ?>
                        <option value="SHOW ALL">SHOW ALL</option>
                    <?php endif; ?>
                <?php foreach($subjects as $subject) : ?>
                    <option value="<?php print $subject; ?>"><?php print $subject; ?></option>
                <?php endforeach; ?>

            </select>
            <label><strong><?php print "Showing " . count($cars) . " case review(s)."; ?></strong></label>
        </form>
    </div>

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
                <label><strong>Result: </strong><?php print $car->getResult(); ?></label><br /><br />
                <a href="<?php print $car->getUrl(); ?>">View on the Library of Defense website</a>
            </div>

        <?php endforeach; ?>
    <?php else : ?>

        <div class="car-container" style="text-align: center;">
            <h1>No case reviews were found.</h1>
        </div>

    <?php endif; ?>

</div> <!-- end list container -->

<script>
    function submitForm(){

        document.getElementById("filter-form").submit();
    }
</script>




