
<?php

use function Html\createElement;

$subjectDefault = array("" => "All Subjects");
$allSubjects = $subjectDefault + $subjects;

$yearDefault = array("" => "All Years");
$allYears = $yearDefault + $years;

$countyDefault = array("" => "All Counties");
$allCounties = $countyDefault + $counties;

$selectedSubject = empty($subject) ? "Show All" : $subject;
$selectedYear = empty($year) ? "All Years" : $year;
$selectedCounty = empty($county) ? "All Counties" : $county;

print createElement("datalist", array("id" => "judge-datalist", "name" => "judge-datalist", "options" => $judges));

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">
    <label><strong>Filter:</strong></label>

    <?php print createElement("select", array("name" => "subject_1", "options" => $allSubjects, "selected" => $selectedSubject)); ?>

    <?php print createElement("select", array("name" => "year", "options" => $allYears, "selected" => $selectedYear)); ?>

    <?php print createElement("select", array("name" => "circuit", "options" => $allCounties, "selected" => $selectedCounty)); ?>

    <input autocomplete="off" type="text" name="judges" value="<?php print $judgeName; ?>" data-datalist="judge-datalist" placeholder="Search by judge name" onchange="submitForm()" />

    <?php if(True || $user->isAdmin()) : ?>
        <a href="/car/list">Clear</a>
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