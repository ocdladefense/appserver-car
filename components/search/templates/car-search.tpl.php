
<?php


?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/components/search/component.css" />

<form id="car-search" class="form-inline" action="/car/list" method="post">

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
        <?php print Html\Button("resetBtn","Reset"); ?>
    </div>
    

    <div class="form-item">
        <?php print Html\Checkbox("summarize", $summarize ? true : false); ?>
        <label class="checkbox-label">summarize</label>
    </div>


</form>

<script>

    var submissionNodes = document.getElementsByTagName("select");

    for(var i = 0; i < submissionNodes.length; i++){

        submissionNodes[i].addEventListener("change", function(){
            var form = document.getElementById("car-search");
            form.submit();
        });
    }

    document.getElementById("summarize").addEventListener("change", function(){
        var form = document.getElementById("car-search");
        form.submit();
    });


    document.getElementById("resetBtn").addEventListener("click", function(){
        window.location.replace();
    });
    
    
</script>