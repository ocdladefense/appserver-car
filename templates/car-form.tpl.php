

<?php $headerMessage = "Create a New Case Review Record"; ?>

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-form.css" />


<div id="form-container" class="car-form-container">

    <a href="/car/list" style="float: left;">back to list</a>

    <h1 id="car-form-header" class="car-form-header"><?php print $headerMessage; ?></h1>

    <form id="car-form" class="car-form" action="/car/save" method="post">

        <input type="hidden" name="id" value="<?php print $car->getId(); ?>" />
        
        <div class="car-title">
            <label>Title</label>
            <input class="form-row" name="plaintiff" value="<?php print $car->getPlaintiff(); ?>" placeholder="Enter Plaintiff" />
            <label class="form-row">vs.</label>
            <input class="form-row" name="defendant" value="<?php print $car->getDefendant(); ?>" placeholder="Enter Defendant" />
        </div>

        <label>Decision Date</label>
        <input class="form-row two-digit" name="month" value="<?php print $car->getMonth(); ?>" maxlength=2 placeholder="mm" />
        <input class="form-row two-digit" name="day" value="<?php print $car->getDay(); ?>" maxlength=2 placeholder="dd" />
        <input class="form-row four-digit" name="year" value="<?php print $car->getYear(); ?>" maxlength=4 placeholder="yyyy" />

        <label>Citation</label>
        <input name="citation" value="<?php print $car->getCitation(); ?>" placeholder="Enter Citation...(ex. 311 Or App 542)" />

        <label>Primary Subject</label>
        <input name="subject_1"value="<?php print $car->getSubject1(); ?>" placeholder="Enter Primary Subject..." />

        <label>Secondary Subject</label>
        <input name="subject_2" value="<?php print $car->getSubject2(); ?>" placeholder="Enter Secondary Subject..." />

        <label>Circuit</label>
        <input name="circuit" value="<?php print $car->getCircuit(); ?>" placeholder="Enter Circuit..." />
        
        <label>Majority</label>
        <input name="majority" value="<?php print $car->getMajority(); ?>" placeholder="Enter majority names..." />

        <label>Judges</label>
        <input name="judges" value="<?php print $car->getJudges(); ?>" placeholder="Enter additional judges..." />

        <label>Summary</label>
        <textarea name="summary" placeholder="Enter the case summary..."><?php print $car->getSummary(); ?></textarea>

        <label>Result</label>
        <textarea name="result" placeholder="Enter the result..."><?php print $car->getResult(); ?></textarea>

        <label>L.O.D. URL</label>
        <input name="url" value="<?php print $car->getUrl(); ?>" placeholder="Enter the url..." />

        <button type="submit" id="submit">Submit</button>
    </form>
</div>