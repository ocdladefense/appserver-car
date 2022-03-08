<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-list.css" />


<h1 class="list-header">OCDLA Criminal Appellate Review Summaries</h1>

	<div class="car-container">
       <?php component('SearchWidget'); ?>
    </div>

	<div class="car-container">
		
		<?php component('MessageWidget'); ?>
	</div>



<div id="car-list-container" class="list-containter">

	<?php print $list; ?>

</div> <!-- end list container -->


<script src="<?php print module_path(); ?>/assets/js/car-flag.js">
</script>
<script src="<?php print module_path(); ?>/assets/js/car.js">
</script>

