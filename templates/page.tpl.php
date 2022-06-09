<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/list.css" />


<h1 class="list-header">OCDLA Criminal Appellate Review Summaries</h1>



<div class="car-container">
	<?php component('SearchWidget'); ?>
</div>

<div class="car-container">
	<?php component('MessageWidget'); ?>
</div>


<div id="search-meta">
    <div id="search-query">Query: <?php print $query; ?></div>
	<div id="search-count">Showing <?php print $count; ?> results.</div>
</div>



<?php /*
	<?php if(empty($records)): ?>
		
		<div class="car-container" style="text-align: center;">
			<h1>No case reviews were found.</h1>
		</div>

	<?php endif; ?>
	*/
?>


<div id="car-list-container" class="list-container">

	<?php if($results): ?>
			<?php print $list; ?>
	<?php else: ?>

			Your search returned no results.

	<?php endif; ?>

</div> <!-- end list container -->

<script src="<?php print module_path(); ?>/assets/js/admin.js">
</script>


