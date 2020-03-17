<style type="text/css">
    .car-status{
        display:table-row;
    }
    .car-status-field-container{
        display:table-cell;
    }
</style>




<?php

foreach($statuses as $status){
    $date = $status->getDate();
    $message = $status->getMessage();
    $url = $status->getSelectedUrl();
    $statusCode = $status->getStatusCode();
    $runtime = $status->getRuntime();

?>

<div class= "car-status">
    <div class= "car-status-field-container">
        <div class="status-date">
            <?php print $date; ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-message">
            <?php print $message; ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-url">
            <a href="<?php print $url; ?>"> <?php print $url; ?> </a>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-code">
            <?php print $statusCode; ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-runtime">
            <?php print $runtime; ?>
        </div>
    </div>
</div>


<?php }; ?>