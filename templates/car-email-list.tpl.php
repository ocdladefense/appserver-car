
<?php foreach($cars as $car) : 

    $title = ucwords($car->getSubject1()) . " - " . $car->getSubject2();
    $importance = $car->getImportance() == 0 ? "unset" : $car->getImportance() . "/5";
    $citation = $car->getCitation();
    $date = $car->getDate(false);
    $aJudge = $car->getAppellateJudge();
    $county = $car->getCircuit() . " County";
    $tJudge = $car->getTrialJudge();
?>

    <h4><?php print $title; ?></h4>
    <p><strong><?php print "Importance: " . $importance; ?></strong></p>
    <p><?php print nl2br($car->getSummary()); ?></p>
    <p>
        <a href="<?php print $car->getExternalLink(); ?>"><?php print $car->getTitle(); ?></a>
        <?php print "$citation ($date) ($aJudge) ($county, $tJudge)"; ?>
    </p>

    ----------------------------------------------------------------------------------------------------------------

<?php endforeach; ?>