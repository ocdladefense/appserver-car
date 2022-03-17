<?php
/**
 * 
 * @template email-list
 * 
 * @description Template file to generate HTMl for a list of Criminal Appellate Reviews.
 *  The generated HTML is intended for use inside the body of an HTML-formatted email.
 * 
 */

foreach($cars as $car): 

    $title = ucwords($car->getSubject1()) . " - " . $car->getSubject2();
    $importance = $car->getImportance() == 0 ? "unset" : $car->getImportance() . "/5";
    $citation = $car->getCitation();
    $date = $car->getDate(false);
    $aJudge = $car->getAppellateJudge();
    $county = $car->getCounty() . " County";
    $tJudge = $car->getTrialJudge();
?>



    <h4 style="font-size:12pt;">
        <?php print $title; ?>
        <br />
        <a href="https://ocdla.app/car/list/<?php print $car->getId(); ?>">
            <?php print $car->getTitle(); ?>
        </a>
        <br />
        <span style="font-weight:bold;">Important: <?php print $importance; ?></span>
    </h4>

    <p style="font-size:11pt;">
        <?php print nl2br($car->getSummary()); ?>
    </p>
    <p style="font-size:11pt;">
        <a href="https://ocdla.app/car/list/<?php print $car->getId(); ?>">
            <?php print $car->getTitle(); ?>
        </a>
        <br />
        <?php print "$citation ($date) ($aJudge) ($county, $tJudge)"; ?>
        <br />
        -------------------------------------
    </p>

    

<?php endforeach; ?>