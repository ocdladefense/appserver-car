<?php


    $ocdlaLatitude = "44.044495";
    $ocdlaLongitude = "-123.090919";

    $appURL = "https://ocdla.app/car/list?year=$year&month=$month&day=$day";
?>

<!--<img src="https://ocdla.app/content/images/criminal-appellate-review.png" alt="Appellate Court image" />-->

<p>Dear OCDLA Members,</p>
 
<p>Below find recent opinion summaries.</p> 



<p>
    <a href="<?php print $appURL; ?>">Oregon Court of Appeals - <?php print $date; ?></a>
</p>

<p>-Rankin Johnson</p>

<?php print $carList; ?>

<br />


<p>Enjoy,</p>
<p>
    /s/ Rankin Johnson
    <br />
    Oregon Criminal Defense Lawyers Association
    <br />
    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php print "$ocdlaLatitude,$ocdlaLongitude"; ?>">
        101 East 14th Avenue, Eugene, OR 97401
    </a>
    <br />
    <a href="tel:503.504.2060">503.504.2060</a>
    <br />
    <a href="https://www.ocdla.org">www.ocdla.org</a>
</p>