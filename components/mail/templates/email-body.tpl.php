<?php


    $ocdlaLatitude = "44.044495";
    $ocdlaLongitude = "-123.090919";

    $appURL = APP_URL . "/car/list?decision_date={$decision_date}&court=$court";
?>

<p>&nbsp;</p>

<p>Dear OCDLA Members,</p>
 
<p>Below find recent opinion summaries.</p> 



<p>
    <a target="_new" href="<?php print $appURL; ?>">
        <?php print $court; ?> - <?php print $date; ?>
    </a>
</p>

<p>-Rankin Johnson</p>

<?php print $list; ?>

<br />


<p>Enjoy,</p>
<p>
    /s/ Rankin Johnson
    <br />
    Oregon Criminal Defense Lawyers Association
    <br />
    <a target="_new" href="https://www.google.com/maps/dir/?api=1&destination=<?php print "$ocdlaLatitude,$ocdlaLongitude"; ?>">
        101 East 14th Avenue, Eugene, OR 97401
    </a>
    <br />
    <a href="tel:503.504.2060">503.504.2060</a>
    <br />
    <a target="_new" href="https://www.ocdla.org">www.ocdla.org</a>
</p>