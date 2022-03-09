<?php
/**
 * @component Search Widget
 * 
 * Display a search widget with the appropriate filters for the given object.
 */
?>

<form id="record-search" class="form-inline" action="/car/list" method="post">

    <?php print Html\DataList("judges", $this->getJudges()); ?>


    <div class="form-item">
        <?php print Html\Select("subject", $this->getSubjects(), $subject); ?>
    </div>

    <div class="form-item">
        <?php print Html\Date("decision_date", $min_date); ?>
    </div>

    <div class="form-item">
        <?php print Html\Select("county", $this->getCounties(), $county); ?>
    </div>

    <div class="form-item">
        <?php print Html\Select("court", $this->getCourts(), $court); ?>
    </div>


    <div class="form-item">
        <?php print Html\Select("importance", $this->getRanks(), $rank); ?>
    </div>

    <div class="form-item">
        <?php print Html\Autocomplete("appellate_judge", "judges", $appellate_judge, "Appellate Judge"); ?>
    </div> 

    <div class="form-item">
        <?php print Html\Autocomplete("trial_judge", "judges", $trial_judge, "Trial Judge"); ?>
    </div>


    <div class="form-item">
        <?php print Html\Form\Submit("search-submit","Submit"); ?>
    </div>

    <div class="form-item">
        <?php print Html\Button("search-reset","Reset"); ?>
    </div>

    <div class="form-item">
        <?php print Html\Button("search-clear","Clear"); ?>
    </div>
    

    <div class="form-item">
        <?php print Html\Button("summarize", "Summarize", $summarize ? true : false); ?>
    </div>


</form>

