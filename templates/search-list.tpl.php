
<?php
// This is the form / list header that is at the top of the car list.  
// It has all of the functionality for filtering case review lists. 


// $subject = The currently selected subject.
// $year = The currently selected year.
// $subjects = All of the subjects.
// $years = All of the available years.

// $yearDefault = array("", "All Years");
// $allOptions = $yearDefault + 

$yearDefault = array("" => "All Years");
$allYears = $yearDefault + $years;

$subjectDefault = array("" => "All Subjects");
$allSubjects = $subjectDefault + $subjects;

$selectedSubject = empty($subject) ? "Show All" : $subject;
$selectedYear = empty($year) ? "All Years" : $year;

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">
    <label><strong>Filter:</strong></label>

    <select id="subject_1" name="subject_1" style="text-align:center;" onchange="submitListSearchForm()">
        
        <?php foreach($allSubjects as $value => $label) { 
            
            $selected = $selectedSubject == $value ? "selected" : ""; 
        ?>

        <option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>


        <?php } ?>
        
    </select>

    <select id="year" name="year" onchange="submitListSearchForm()">
        
    <?php foreach($allYears as $value => $label) { 
            
            $selected = $selectedYear == $value ? "selected" : ""; 
        ?>

        <option value="<?php print $value; ?>" <?php print $selected; ?>><?php print $label; ?></option>


        <?php } ?>

    </select>

    <?php if(True || $user->isAdmin()) : ?>
        <a class="add-review" href="/car/new">Add Review <i class="fas fa-plus" aria-hidden="true"></i></a>
    <?php endif; ?>

</form>