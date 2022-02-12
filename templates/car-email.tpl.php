<?php
    $year = $car->getYear();
    $month = $car->getMonth();
    $day = $car->getDay();
    $date = $car->getDate(false);

    $ocdlaLatitude = "44.044495";
    $ocdlaLongitude = "-123.090919";

    $appURL = "/car/list?year=$year&month=$month&day=$day";
?>

<img src="image1.png" alt="Apellate Court image">

<p>Dear OCDLA Members,</p>
 
<p>Below find recent opinion summaries.</p> 

<p>Media release - <?php print $date; ?> <--- This should be a link to the media page</p>

<p>
    The Library of Defense page is here: 
    <a href="<?php print $appURL; ?>">Oregon Court of Appeals - <?php print $date; ?></a>
</p>

<p>-Rankin Johnson</p>

<br />

<?php print $carList; ?>

<br />
<br />

<p>Enjoy,</p>
<p>/s/ Rankin Johnson</p>
<p>Oregon Criminal Defense Lawyers Association</p>

<p>
    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php print "$ocdlaLatitude,$ocdlaLongitude"; ?>">
        101 East 14th Avenue, Eugene, OR 97401
    </a>
</p>

<p>
    <a href="tel:503.504.2060">503.504.2060</a>
</p>

<p><a href="www.ocdla.org">www.ocdla.org</a></p>

<img src="image2.png" alt="OCDLA image">

