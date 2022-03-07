
<?php

use function Html\createDataListElement;
use function Html\createSelectElement;

?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-search.css" />

<div class="search-container">
    <form id="car-search-form" class="car-search-form" action="/car/list" method="post">

        <div class="search-filters">

            <?php /*print createSelectElement("subject", $allSubjects, $subject);*/ ?>
            <?php print $this->getSubjects(); ?>
            <?php /* print createSelectElement("year", $allYears, $year); */ ?>
            <?php /* print createSelectElement("month", $allMonths, $month); */ ?>
            <?php /* print createSelectElement("court", $allCourts, $court); */?>
            <?php /* print createSelectElement("circuit", $allCounties, $county); */?>
            <?php /* print createSelectElement("importance", $importanceLevels, $selectedImportance); */ ?>

            <input autocomplete="off" type="text" name="appellate_judge" value="<?php print $selectedAppellateJudge; ?>" data-datalist="judge-datalist" placeholder="Appellate Judge" onchange="submitForm()" />
            
            <input autocomplete="off" type="text" name="trial_judge" value="<?php print $selectedTrialJudge; ?>" data-datalist="judge-datalist" placeholder="Trial Judge" onchange="submitForm()" />


        </div>

        <div class="search-filters bottom-row">

            <a class="filter-item" href="/car/list">Clear</a>

            <label class="checkbox-label">summarize</label>
            <input id="summarize-checkbox" class="checkbox-option filter-item" type="checkbox" <?php print $summarizeChecked; ?> name="summarize" value="1" />


        </div>
    </form>
</div>

<script>

    var submissionNodes = document.getElementsByTagName("select");

    for(var i = 0; i < submissionNodes.length; i++){

        submissionNodes[i].addEventListener("change", function(){
            $form = document.getElementById("car-search-form");
            $form.submit();
        });
    }

    document.getElementById("summarize-checkbox").addEventListener("change", function(){
            $form = document.getElementById("car-search-form");
            $form.submit();
        });
    
    
</script>