
<?php

use function Html\createDataListElement;
use function Html\createSelectElement;

$subjectDefault = array("" => "All Subjects");
$allSubjects = $subjectDefault + $subjects;

$yearDefault = array("" => "All Years");
$allYears = $yearDefault + $years;

$countyDefault = array("" => "All Counties");
$allCounties = $countyDefault + $counties;

print createDataListElement("judge-datalist", $judges);

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">
    <label><strong>Filter:</strong></label>

    <?php print createSelectElement("subject_1", $allSubjects, $subject); ?>

    <?php print createSelectElement("year", $allYears, $year); ?>

    <?php print createSelectElement("circuit", $allCounties, $county); ?>

    <input autocomplete="off" type="text" name="judges" value="<?php print $judgeName; ?>" data-datalist="judge-datalist" placeholder="Search by judge name" onchange="submitForm()" />

    <?php if(True || $user->isAdmin()) : ?>
        <a href="/car/list">Clear</a>
        <a class="add-review" href="/car/new">Add Review <i class="fas fa-plus" aria-hidden="true"></i></a>
    <?php endif; ?>

</form>

<?php if($user->isAdmin()) : ?>
    <div>
        <h3><?php print $query; ?></h3>
    </div>
<?php endif; ?>

<script>

    var submissionNodes = document.getElementsByTagName("select");

    for(var i = 0; i < submissionNodes.length; i++){

        submissionNodes[i].addEventListener("change", function(){
            $form = document.getElementById("filter-form");
            $form.submit();
        });
    }

    
</script>