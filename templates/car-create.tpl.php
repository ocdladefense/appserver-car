<script>
    const isUpdate = <?php $update ? print($update) : print("false") ?>;
    const car = <?php $car ? print($car) : print("null") ?>;
    const token = "<?php print($token) ?>";
    const newFields = <?php print($newFieldsJson) ?>;
    const existingFields = <?php print($listOptionsJson) ?>;
</script>

<h1><?php $update ? print("Update") : print("Create") ?> Criminal Apellate Review</h1>

<div id="car-create-results">

</div>

<div id="car-create-content">

</div>