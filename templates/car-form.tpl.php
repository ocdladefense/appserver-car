

<?php $headerMessage = "Create a New Case Review Record"; ?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-form.css" />


<div id="form-container" class="car-form-container">

    <a class="back-link" href="/car/list" style="float: left;">back to list</a>

    <h1 id="car-form-header" class="car-form-header"><?php print $headerMessage; ?></h1>

    <?php
        $checkFlagged = $car->isFlagged() ? "checked" : "";
        $checkTest = $car->isTest() ? "checked" : "";
        $checkDraft = $car->isDraft() ? "checked" : "";
    ?>

    <form id="car-form" class="car-form" action="/car/save" method="post">

        <?php if($car->getId() != null) : ?>

            <div class="form-item">
                <a class="delete-review" href="/car/delete/<?php print $car->getId(); ?>">Delete</a>
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
                <input class="checkbox-option" name="is_test" value="1" type="checkbox" checked />
                <label class="checkbox-label">Is Test</label>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php print $car->getId(); ?>" />

        <div class="decision-date form-item">
            <label>Decision Date</label>
            <input type="text" class="form-row two-digit" name="month" style="text-align:center;" value="<?php print $car->getMonth(); ?>" maxlength=2 placeholder="mm" />
            <input type="text" class="form-row two-digit" name="day" style="text-align:center;" value="<?php print $car->getDay(); ?>" maxlength=2 placeholder="dd" />
            <input type="text" class="form-row four-digit" name="year" style="text-align:center;" value="<?php print $car->getYear(); ?>" maxlength=4 placeholder="yyyy" />
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
            <label>Citation</label>
            <input type="text" name="citation" value="<?php print $car->getCitation(); ?>" placeholder="Enter Citation...(ex. 311 Or App 542)" />
        </div>

        <div class="form-item">
            <label>Primary Subject</label>
            <input type="text" name="subject_1"value="<?php print $car->getSubject1(); ?>" placeholder="Enter Primary Subject..." />
        </div>

        <div class="form-item">
            <label>Secondary Subject</label>
            <input type="text" name="subject_2" value="<?php print $car->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />
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