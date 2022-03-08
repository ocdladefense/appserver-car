

<?php

    use function Html\createElement;
    use function Html\createDataListElement;
    use function Html\createSelectElement;

    $isUpdate = $car->getId() != null;
    $headerMessage = $isUpdate ? "Update Case Review" : "Create a Case Review";
?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-form.css" />


<div id="form-container" class="car-form-container">

    <a class="back-link" href="/car/list" style="float: left;"><i class="fa fa-arrow-left" style="font-size:48px;color:blue"></i></a><br /><br />

    <h1 id="car-form-header" class="car-form-header"><?php print $headerMessage; ?></h1>

    <?php
        $checkFlagged = $car->isFlagged() ? "checked" : "";
        
        $checkDraft = $car->isDraft() ? "checked" : "";

        $subjectDefault = array("" => "None Selected");
        $allSubjects = $subjectDefault + $subjects;

        $selectedCourt = empty($car->getCourt()) ? "" : $car->getCourt();
        $allCourts[""] = "none selected";

        $countyDefault = array("" => "None Selected");
        $allCounties = $countyDefault + $counties;

        $selectedSubject = empty($car->getSubject1()) ? "" : $car->getSubject1();

        $selectedSubject = ucwords($selectedSubject);

        $selectedCounty = empty($car->getCircuit()) ? "" : $car->getCircuit();

        // Create the datalist element for the judge name autocomplete.
        print createDataListElement("judge-datalist", $judges);

        $importanceLevels = array(
            "" => "none selected",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5"
        );

        $selectedImportance = !empty($car->getImportance()) ? $car->getImportance() : "";

    ?>

    <form id="car-form" class="car-form" action="/car/save" method="post">



        <?php if($car->getId() != null) : ?>

            <a class="delete-review" data-car-id="<?php print $car->getId(); ?>" href="/car/delete/<?php print $car->getId(); ?>">
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

        <input type="hidden" name="id" value="<?php print $car->getId(); ?>" />

        <div>
            <label>Decision Date</label>
            <input required type="date" name="date" value="<?php print $car->getPickerCompatibleDate(); ?>">
        </div>

        <div>
            <label>Importance</label>
            <?php print createSelectElement("importance", $importanceLevels, $selectedImportance); ?>
        </div>

        <div>
            <label>Court</label>
            <?php print createSelectElement("court", $allCourts, $selectedCourt); ?>
        </div>


        <div>
            <label>Plaintiff</label>
            <input required type="text" name="plaintiff" value="<?php print empty($car->getPlaintiff()) ? "State" : $car->getPlaintiff(); ?>" placeholder="Enter plaintiff..." />
        </div>

        <div>
            <label>Defendant</label>
            <input required type="text" name="defendant" value="<?php print $car->getDefendant(); ?>" placeholder="Enter defendant..." />
        </div>

        <div>
            <label>Appellate Judge</label>
            <input required autocomplete="off" type="text" name="appellate_judge" value="<?php print $car->getAppellateJudge(); ?>" data-datalist="judge-datalist" placeholder="Search by judge name" />
        </div>

        <div>
            <label>Trial Judge</label>
            <input required autocomplete="off" type="text" name="trial_judge" value="<?php print $car->getTrialJudge(); ?>" data-datalist="judge-datalist" placeholder="Search by judge name" />
        </div>

        <div>
            <label>Primary Subject</label>
            <?php print createSelectElement("subject", $allSubjects, $selectedSubject); ?>
            <button type="button" id="new-subject" class="new-subject" onclick="handleNewSubject()">New Subject</button>
        </div>

        <div>
            <label>Secondary Subject</label>
            <input type="text" name="secondary_subject" value="<?php print $car->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />
        </div>

        <div>
            <label>Appellate #</label>
            <input required type="text" name="a_number" value="<?php print $car->getA_number(); ?>" placeholder="Enter A#" />
        </div>


        <div>
            <label>Citation</label>
            <input required type="text" name="citation" value="<?php print $car->getCitation(); ?>" placeholder="Enter Citation...(ex. 311 Or App 542)" />
        </div>

        <div>
            <label>County</label>
            <?php print createSelectElement("circuit", $allCounties, $selectedCounty); ?>
        </div>

        <div>
            <label>Link to review on State of Oregon Law Library</label>
            <input required type="text" name="external_link" value="<?php print $car->getExternalLink(); ?>" placeholder="Link to State of Oregon Law Library..." />
        </div>

        <div>
            <label>Summary</label>
            <textarea required name="summary" placeholder="Enter the entire case summary, including additional information..."><?php print $car->getSummary(); ?></textarea>
        </div>


        <button type="submit" id="submit">Submit</button>
    </form>
</div>

<script src="<?php print module_path(); ?>/assets/js/car.js"></script>