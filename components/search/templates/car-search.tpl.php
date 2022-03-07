
<?php


?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-search.css" />

<div class="search-container">
    <form id="car-search-form" class="car-search-form" action="/car/list" method="post">


        <?php /*print createSelectElement("subject", $allSubjects, $subject);*/ ?>

        <div class="form-item">

            <?php

                // var_dump($this->getSubjects());exit;
                print Html\Select("subject", $this->getSubjects(), $this->getInput("subject"));
                
            ?>
        </div>

        <div class="form-item">
            <?php $max = null; ?>
            <?php 
                // print Html\Date("decision_date", "2018-01-01", $max);
            ?>
        </div>

        <div class="form-item">
            <?php /* print Html\Select("court", $allCourts, $court); */?>
        </div>

        <div class="form-item">
            <?php /* print Html\Select("county", $allCounties, $county); */?>
        </div>

        <div class="form-item">
            <?php
                // print Html\DataList("judge-datalist", $this->getJudges(), $this->request->judge);
            ?>
        </div>

        <div class="form-item">
            <?php /* print Html\Select("importance", $importanceLevels, $selectedImportance); */ ?>
        </div>


        <div class="form-item">
            <input autocomplete="off" type="text" name="appellate_judge" value="<?php print $selectedAppellateJudge; ?>" data-datalist="judge-datalist" placeholder="Appellate Judge" onchange="submitForm()" />
        </div>

        <div class="form-item">
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