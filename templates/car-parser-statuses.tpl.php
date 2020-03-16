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
            <?php print($date); ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-message">
            <?php print($message); ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-url">
            <?php print($url); ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-code">
            <?php print($statusCode); ?>
        </div>
    </div>
    <div class= "car-status-field-container">
        <div class="status-runtime">
            <?php print($runtime); ?>
        </div>
    </div>
</div>


<?php }; ?>