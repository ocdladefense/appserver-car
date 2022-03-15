<?php
    $isUpdate = $record->getId() != null;
    $headerMessage = $isUpdate ? "Update record" : "Create record";
?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/form.css" />


<div id="form-container" class="form-container">


    <a class="back-link" href="/car/list" style="float: left;">
        <i class="fa fa-arrow-left"></i>
    </a>&nbsp;

    <h1 class="page-title">
        <?php print $headerMessage; ?>
    </h1>



    <?php

        // Create the datalist element for the judge name autocomplete.
        print Html\Datalist("judges", $judges);

        $importanceLevels = array(
            "" => "none selected",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5"
        );

    ?>

 

    <form id="record-form" class="record-form" action="/car/save" method="post">

        <input type="hidden" name="id" value="<?php print $record->getId(); ?>" />

        <?php if($record->getId() != null) : ?>

            <a class="delete-review" data-car-id="<?php print $record->getId(); ?>" href="/car/delete/<?php print $record->getId(); ?>">
                <i style="font-size: x-large;" class="fas fa-trash-alt"></i>
            </a>

        <?php endif; ?>



        <div class="form-group">
            <div class="form-item">
                <input class="checkbox-option" name="is_flagged" value="1" <?php print $checkFlagged; ?> type="checkbox" />
                <label class="checkbox-label">Flag</label>
            </div>

            <div class="form-item">
                <input class="checkbox-option" name="is_draft" value ="1" <?php print $checkDraft; ?> type="checkbox" />
                <label class="checkbox-label">Is Draft</label>
            </div>
        </div>

        

        <div class="form-group">
            <div class="form-item">
                <label>Decision Date</label>
                <input required type="date" name="decision_date" value="<?php print $record->getDecisionDate(); ?>">
            </div>

            <div class="form-item">
                <label>Importance</label>
                <?php print Html\Select("importance", $importanceLevels, $importance); ?>
            </div>

            <div class="form-item">
                <label>Court</label>
                <?php print Html\Select("court", $courts, $court); ?>
            </div>
        </div>


        <div class="form-group">
            <div class="form-item">
                <label>Plaintiff</label>
                <input required type="text" name="plaintiff" value="<?php print empty($record->getPlaintiff()) ? "State" : $record->getPlaintiff(); ?>" placeholder="Enter plaintiff..." />
            </div>

            <div class="form-item">
                <label>Defendant</label>
                <input required type="text" name="defendant" value="<?php print $record->getDefendant(); ?>" placeholder="Enter defendant..." />
            </div>
        </div>



        <div class="form-group">
            <div class="form-item">
                <label>Appellate Judge</label>
                <input required autocomplete="off" type="text" name="appellate_judge" value="<?php print $record->getAppellateJudge(); ?>" list="judges" placeholder="Search by judge name" />
            </div>

            <div class="form-item">
                <label>Trial Judge</label>
                <input required autocomplete="off" type="text" name="trial_judge" value="<?php print $record->getTrialJudge(); ?>" list="judges" placeholder="Search by judge name" />
            </div>

            <div class="form-item">
                <label>Alt Judge</label>
                <input required autocomplete="off" type="text" name="alt_judge" value="" list="judges" placeholder="Search by judge name" />
            </div>
        </div>


        <?php if(false && $alt_judges): ?>
            <?php foreach($alt_judges as $judge): ?>

            <div class="form-group">
                <div class="form-item">
                    <label><?php print $judge["label"]; ?></label>
                    <input required autocomplete="off" type="text" name="alt_judge" value="" list="judges" placeholder="Search by judge name" />
                </div>
            </div>

            <?php endforeach; ?>
        <?php endif; ?>

        
        <div class="form-group">
            <div class="form-item">
                <label>Primary Subject</label>
                <?php print Html\Select("subject", $subjects, $subject); ?>
                <button type="button" id="new-subject" class="new-subject" onclick="handleNewSubject()">New Subject</button>
            </div>

            <div class="form-item">
                <label>Secondary Subject</label>
                <input type="text" name="secondary_subject" value="<?php print $record->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />
            </div>
        </div>


        <div class="form-group">
            <div class="form-item">
                <label>Appellate #</label>
                <input required type="text" name="a_number" value="<?php print $record->getA_number(); ?>" placeholder="Enter A#" />
            </div>


            <div class="form-item">
                <label>Citation</label>
                <input required type="text" name="citation" value="<?php print $record->getCitation(); ?>" placeholder="Enter Citation...(e.g. 311 Or App 542)" />
            </div>

            <div class="form-item">
                <label>County</label>
                <?php print Html\Select("county", $counties, $county); ?>
            </div>
        </div>




        <div class="form-group">
            <div class="form-item form-item-summary">
                <label>Summary</label>
                <textarea cols="100" rows="15" required name="summary" placeholder="Enter the entire case summary."><?php print $record->getSummary(); ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="form-item form-item-link">
                <label>SOLL Link</label>
                <input required size="100" type="text" name="external_link" value="<?php print $record->getExternalLink(); ?>" placeholder="SOLL Link" />
            </div>
        </div>

        <div class="form-item">
            <button type="submit" id="submit">Submit</button>
        </div>
    </form>
</div>

<script src="<?php print module_path(); ?>/assets/js/form.js">
</script>

<script src="<?php print module_path(); ?>/assets/js/admin.js">
</script>