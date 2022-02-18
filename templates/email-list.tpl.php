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
    $county = $car->getCircuit() . " County";
    $tJudge = $car->getTrialJudge();
?>



    <h4>
        <?php print $title; ?>
    </h4>

    <p style="font-weight: bold;">
        Important: <?php print $importance; ?>
    </p>
    <p>
        <?php print nl2br($car->getSummary()); ?>
    </p>
    <p>
        <a href="https://ocdla.app/car/list/<?php print $car->getId(); ?>">
            <?php print $car->getTitle(); ?>
        </a>
        <?php print "$citation ($date) ($aJudge) ($county, $tJudge)"; ?>
        <br />
        -------------------------------------
    </p>

    

<?php endforeach; ?>