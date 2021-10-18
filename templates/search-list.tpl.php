
<?php

use function Html\createSelectListElement;
use function Html\createOptionDataList;

$subjectDefault = array("" => "All Subjects");
$allSubjects = $subjectDefault + $subjects;

$yearDefault = array("" => "All Years");
$allYears = $yearDefault + $years;

$countyDefault = array("" => "All Counties");
$allCounties = $countyDefault + $counties;

$selectedSubject = empty($subject) ? "Show All" : $subject;
$selectedYear = empty($year) ? "All Years" : $year;
$selectedCounty = empty($county) ? "All Counties" : $county;

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">
    <label><strong>Filter:</strong></label>

    <?php print createSelectListElement("subject_1", $selectedSubject, $allSubjects); ?>

    <?php print createSelectListElement("year", $selectedYear, $allYears); ?>

    <?php print createSelectListElement("circuit", $selectedCounty, $allCounties); ?>

    <?php if(True || $user->isAdmin()) : ?>
        <a class="add-review" href="/car/new">Add Review <i class="fas fa-plus" aria-hidden="true"></i></a>
    <?php endif; ?>

</form>

<script>

    var submissionNodes = document.getElementsByTagName("select");

    for(var i = 0; i < submissionNodes.length; i++){

        submissionNodes[i].addEventListener("change", function(){
            $form = document.getElementById("filter-form");
            $form.submit();
        });
    }

    
</script>