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

    $subjects = ucwords($car->getSubject1()) . " - " . $car->getSubject2();
    $importance = $car->getImportance() == 0 ? "unset" : $car->getImportance() . "/5";
    $citation = $car->getCitation();
    $date = $car->getDate(false);
    $aJudge = $car->getAppellateJudge();
    $county = $car->getCounty() . " County";
    $tJudge = $car->getTrialJudge();
    $link = APP_URL . "/car/list/" . $car->getId();
?>


    <div class="car-summary">
        <h4 style="font-size:12pt;">
            <?php print $subjects; ?>
            <br />
            <a target="_new" href="<?php print $link; ?>">
                <?php print $car->getTitle(); ?>
            </a>
            <br />
            <span style="font-weight:bold;">Importance: <?php print $importance; ?></span>
        </h4>

        <p style="font-size:11pt;">
            <?php print nl2br($car->getSummary()); ?>
        </p>

        <p style="font-size:11pt;">
            <?php print "$citation ($date) ($aJudge) ($county, $tJudge)"; ?>
            <br />
            -------------------------------------
        </p>
    </div>
    

<?php endforeach; ?>