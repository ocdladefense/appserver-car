<?php
    $isUpdate = $record->getId() != null;
    $headerMessage = $isUpdate ? "Update record" : "Create record";
?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/form.css" />


<div id="form-container" class="car-form-container">

    <a class="back-link" href="/car/list" style="float: left;"><i class="fa fa-arrow-left" style="font-size:48px;color:blue"></i></a>

    <h1 id="car-form-header" class="car-form-header">
        <?php print $headerMessage; ?>
    </h1>



    <?php

        // Create the datalist element for the judge name autocomplete.
        print Html\Datalist("judge-datalist", $judges);

        $importanceLevels = array(
            "" => "none selected",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5"
        );

    ?>

 

    <form id="car-form" class="car-form" action="/car/save" method="post">

        <?php if($record->getId() != null) : ?>

            <a class="delete-review" data-car-id="<?php print $record->getId(); ?>" href="/car/delete/<?php print $record->getId(); ?>">
                <i style="font-size: x-large;" class="fas fa-trash-alt"></i>
            </a>

        <?php endif; ?>

        <div class="checkbox-area">
            <div>
                <input class="checkbox-option" name="is_flagged" value="1" <?php print $checkFlagged; ?> type="checkbox" />
                <label class="checkbox-label">Flag</label>
            </div>

            <div>
                <input class="checkbox-option" name="is_draft" value ="1" <?php print $checkDraft; ?> type="checkbox" />
                <label class="checkbox-label">Is Draft</label>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php print $record->getId(); ?>" />

        <div>
            <label>Decision Date</label>
            <input required type="date" name="date" value="<?php print $record->getPickerCompatibleDate(); ?>">
        </div>

        <div>
            <label>Importance</label>
            <?php print Html\Select("importance", $importanceLevels, $selectedImportance); ?>
        </div>

        <div>
            <label>Court</label>
            <?php print Html\Select("court", $allCourts, $selectedCourt); ?>
        </div>


        <div>
            <label>Plaintiff</label>
            <input required type="text" name="plaintiff" value="<?php print empty($record->getPlaintiff()) ? "State" : $record->getPlaintiff(); ?>" placeholder="Enter plaintiff..." />
        </div>

        <div>
            <label>Defendant</label>
            <input required type="text" name="defendant" value="<?php print $record->getDefendant(); ?>" placeholder="Enter defendant..." />
        </div>

        <div>
            <label>Appellate Judge</label>
            <input required autocomplete="off" type="text" name="appellate_judge" value="<?php print $record->getAppellateJudge(); ?>" data-datalist="judge-datalist" placeholder="Search by judge name" />
        </div>

        <div>
            <label>Trial Judge</label>
            <input required autocomplete="off" type="text" name="trial_judge" value="<?php print $record->getTrialJudge(); ?>" data-datalist="judge-datalist" placeholder="Search by judge name" />
        </div>

        <div>
            <label>Primary Subject</label>
            <?php print Html\Select("subject", $allSubjects, $selectedSubject); ?>
            <button type="button" id="new-subject" class="new-subject" onclick="handleNewSubject()">New Subject</button>
        </div>

        <div>
            <label>Secondary Subject</label>
            <input type="text" name="secondary_subject" value="<?php print $record->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />
        </div>

        <div>
            <label>Appellate #</label>
            <input required type="text" name="a_number" value="<?php print $record->getA_number(); ?>" placeholder="Enter A#" />
        </div>


        <div>
            <label>Citation</label>
            <input required type="text" name="citation" value="<?php print $record->getCitation(); ?>" placeholder="Enter Citation...(ex. 311 Or App 542)" />
        </div>

        <div>
            <label>County</label>
            <?php print Html\Select("circuit", $allCounties, $selectedCounty); ?>
        </div>

        <div>
            <label>Link to review on State of Oregon Law Library</label>
            <input required type="text" name="external_link" value="<?php print $record->getExternalLink(); ?>" placeholder="Link to State of Oregon Law Library..." />
        </div>

        <div>
            <label>Summary</label>
            <textarea required name="summary" placeholder="Enter the entire case summary, including additional information..."><?php print $record->getSummary(); ?></textarea>
        </div>


        <button type="submit" id="submit">Submit</button>
    </form>
</div>

<script src="<?php print module_path(); ?>/assets/js/form.js">
</script>