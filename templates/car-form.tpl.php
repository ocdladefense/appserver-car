

<?php
    $isUpdate = $car->getId() != null;
    $headerMessage = $isUpdate ? "Update Case Review" : "Create a Case Review";

    $shouldCheckTest = ($isUpdate && $car->isTest()) || !$isUpdate ? true : false;
?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-form.css" />


<div id="form-container" class="car-form-container">

    <a class="back-link" href="/car/list" style="float: left;"><i class="fa fa-arrow-left" style="font-size:48px;color:blue"></i></a><br /><br />

    <h1 id="car-form-header" class="car-form-header"><?php print $headerMessage; ?></h1>

    <?php
        $checkFlagged = $car->isFlagged() ? "checked" : "";
        $checkTest = $shouldCheckTest ? "checked" : "";
        $checkDraft = $car->isDraft() ? "checked" : "";
    ?>

    <form id="car-form" class="car-form" action="/car/save" method="post">

        <?php if($car->getId() != null) : ?>

            <div class="form-item">
                <a class="delete-review" data-car-id="<?php print $car->getId(); ?>" href="/car/delete/<?php print $car->getId(); ?>"><i style="font-size: x-large;" class="fas fa-trash-alt"></i></a>
            </div>
        <?php endif; ?>

        <div class="checkbox-area">
            <div class="form-item">
                <input class="checkbox-option" name="is_flagged" value="1" <?php print $checkFlagged; ?> type="checkbox" />
                <label class="checkbox-label">Flag</label>
            </div>

            <div class="form-item">
                <input class="checkbox-option" name="is_draft" value ="1" <?php print $checkDraft; ?> type="checkbox" />
                <label class="checkbox-label">Is Draft</label>
            </div>

            <div class="form-item">
                <input class="checkbox-option" name="is_test" value="1" type="checkbox" <?php print $checkTest; ?> />
                <label class="checkbox-label">Is Test</label>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php print $car->getId(); ?>" />

        <div class="decision-date form-item">
            <label>Decision Date</label>
            <input type="date" name="date" value="<?php print $car->getPickerCompatibleDate(); ?>">
        </div>

        <div class="form-item">
            <label>Plaintiff</label>
            <input type="text" name="plaintiff" value="<?php print $car->getPlaintiff(); ?>" placeholder="Enter plaintiff..." />
        </div>

        <div class="form-item">
            <label>Defendant</label>
            <input type="text" name="defendant" value="<?php print $car->getDefendant(); ?>" placeholder="Enter defendant..." />
        </div>

        <div class="form-item">

            <label>Primary Subject</label>

            <select id="select-subject" name="subject_1">

                <?php if(!empty($car->getSubject1())) : ?>
                    <option value="<?php print $car->getSubject1(); ?>" selected><?php print $car->getSubject1(); ?></option>
                <?php endif; ?>
            
                <?php foreach($subjects as $subject) : ?>
                    <option value="<?php print $subject; ?>">
                        <?php print $subject; ?>
                    </option>
                <?php endforeach; ?>
            
            </select>

            <button type="button" id="new-subject" class="new-subject" onclick="handleNewSubject()">New Subject</button>
        </div>

        <div class="form-item">
            <label>Secondary Subject</label>
            <input type="text" name="subject_2" value="<?php print $car->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />
        </div>

        <div class="form-item">
            <label>Citation</label>
            <input type="text" name="citation" value="<?php print $car->getCitation(); ?>" placeholder="Enter Citation...(ex. 311 Or App 542)" />
        </div>

        <div class="form-item">
            <label>Circuit</label>
            <input type="text" name="circuit" value="<?php print $car->getCircuit(); ?>" placeholder="Enter Circuit..." />
        </div>

        <div class="form-item">
            <label>Majority</label>
            <input type="text" name="majority" value="<?php print $car->getMajority(); ?>" placeholder="Enter majority names..." />
        </div>

        <div class="form-item">
            <label>Judges</label>
            <input type="text" name="judges" value="<?php print $car->getJudges(); ?>" placeholder="Enter additional judges..." />
        </div>

        <div class="form-item">
            <label>Summary</label>
            <textarea name="summary" placeholder="Enter the case summary..."><?php print $car->getSummary(); ?></textarea>
        </div>

        <div class="form-item">
            <label>Result</label>
            <textarea name="result" placeholder="Enter the result..."><?php print $car->getResult(); ?></textarea>
        </div>

        <button type="submit" id="submit">Submit</button>
    </form>
</div>

<script src="<?php print module_path(); ?>/assets/js/car.js"></script>