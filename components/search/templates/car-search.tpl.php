
<?php


?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/components/search/component.css" />

<form id="car-search-form" class="car-search-form" action="/car/list" method="post">

    <div class="form-item">
        <?php print Html\Select("subject", $this->getSubjects(), $this->getInput("subject")); ?>
    </div>

    <div class="form-item">
        <?php /* print Html\Date("decision_date", "2018-01-01", $max); */ ?>
    </div>

    <div class="form-item">
        <?php print Html\Select("county", $this->getCounties(), $county); ?>
    </div>

    <div class="form-item">
        <?php print Html\Select("court", $this->getCourts(), $court); ?>
    </div>

    <div class="form-item">
        <?php print Html\DataList("judges", $this->getJudges()); ?>
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
        <a class="filter-item" href="/car/list">Clear</a>
    </div>

    <div class="form-itemm">
        <label class="checkbox-label">summarize</label>
        <?php print Html\Checkbox("summarize", $checked = false); ?>
    </div>


</form>

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