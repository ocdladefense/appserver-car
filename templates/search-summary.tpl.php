
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

$year = ($year == null) ? "All Years" : $year;

$courtMsg = empty($court) ? "" : "in $court";

if(empty($court) || (!empty($court) && empty($date))){
    $dateMsg = "for $year";
    if(!empty($month)) $dateMsg .= "/$month";
    if(!empty($day)) $dateMsg .= "/$day";
}else{
    $dateMsg = "on $date";
}

$showingMessage = "Showing " . $count . " case review(s) $courtMsg $dateMsg.";

?>

<form id="filter-form" class="filter-form" action="/car/summary" method="post">

    <label><strong>Filter by year:</strong></label>

    <select id="year-summary" name="year">
        
    <?php foreach($years as $y) { 
            
            $selected = $year == $y ? "selected" : "";
        ?>

        <option value="<?php print $y; ?>" <?php print $selected; ?>>
            <?php print $y; ?>
        </option>


        <?php } ?>

    </select>

    <label>
        <h3>
            <?php print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$showingMessage"; ?>
    </h3>
    </label>

    <?php if(True || $user->isAdmin()) : ?>
        <a class="add-review" href="/car/new">Add Review <i class="fas fa-plus" aria-hidden="true"></i></a>
    <?php endif; ?>

</form>