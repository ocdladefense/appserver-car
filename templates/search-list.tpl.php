
<?php
// This is the form / list header that is at the top of the car list.  
// It has all of the functionality for filtering case review lists. 


// $subject = The currently selected subject.
// $year = The currently selected year.
// $subjects = All of the subjects.
// $years = All of the available years.


array_unshift($subjects, "Show All");
array_unshift($years, "All Years");
$subject = $subject == null ? "Show All" : $subject;
$year = $year == null ? "All Years" : $year;

?>

<form id="filter-form" class="filter-form" action="/car/list" method="post">
    <label><strong>Filter:</strong></label>

    <select id="subject" name="subject" style="text-align:center;" onchange="submitForm()">
        
        <?php foreach($subjects as $s) { 
            
            $selected = $subject == $s ? "selected" : ""; 
        ?>

        <option value="<?php print $s; ?>" <?php print $selected; ?>>
            <?php print $s; ?>
        </option>


        <?php } ?>
        
    </select>

    <select id="year" name="year" onchange="submitForm()">
        
    <?php foreach($years as $y) { 
            
            $selected = $year == $y ? "selected" : ""; 
        ?>

        <option value="<?php print $y; ?>" <?php print $selected; ?>>
            <?php print $y; ?>
        </option>


        <?php } ?>

    </select>

    <label>
        <strong>
            <?php print "Showing " . $count . " case review(s)."; ?>
        </strong>
    </label>

    <?php if(True || $user->isAdmin()) : ?>
        <a class="add-review" href="/car/new">Add Review <i class="fas fa-plus" aria-hidden="true"></i></a>
    <?php endif; ?>

</form>