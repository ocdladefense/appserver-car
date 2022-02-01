
<?php

use function Html\createDataListElement;
use function Html\createSelectElement;

$subjectDefault = array("" => "All Subjects");
$allSubjects = $subjectDefault + $subjects;

$yearDefault = array("" => "All Years");
$allYears = $yearDefault + $years;

$countyDefault = array("" => "All Counties");
$allCounties = $countyDefault + $counties;

$summarizeChecked = $doSummarize ? "checked" : "";

print createDataListElement("judge-datalist", $judges);

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">

    <div class="search-filters">

        <?php print createSelectElement("subject_1", $allSubjects, $subject); ?>
        <?php print createSelectElement("year", $allYears, $year); ?>
        <?php print createSelectElement("month", $allMonths, $month); ?>
        <?php print createSelectElement("court", $allCourts, $court); ?>
        <?php print createSelectElement("circuit", $allCounties, $county); ?>

        <input autocomplete="off" type="text" name="appellate_judge" value="<?php print $selectedAppellateJudge; ?>" data-datalist="judge-datalist" placeholder="Appellate Judge" onchange="submitForm()" />
        <input autocomplete="off" type="text" name="trial_judge" value="<?php print $selectedTrialJudge; ?>" data-datalist="judge-datalist" placeholder="Trial Judge" onchange="submitForm()" />

        <input id="summarize-checkbox" type="checkbox" <?php print $summarizeChecked; ?> name="summarize" value="1" />
        <label for="summarize">summarize</label>


    </div>

    <div class="search-filters">
        <a href="/car/list">Clear</a>

        <?php if($user->isAdmin()) : ?>
            <a class="add-review" href="/car/new">Add Review<i class="fas fa-plus" aria-hidden="true"></i></a>
        <?php endif; ?>


    </div>
</form>

<script>

    var submissionNodes = document.getElementsByTagName("select");

    for(var i = 0; i < submissionNodes.length; i++){

        submissionNodes[i].addEventListener("change", function(){
            $form = document.getElementById("filter-form");
            $form.submit();
        });
    }

    document.getElementById("summarize-checkbox").addEventListener("change", function(){
            $form = document.getElementById("filter-form");
            $form.submit();
        });
    
    
</script>