<?php
/**
 * Template to render an individual CAR.
 * 
 * Available variables:
 * 
 * $counter - Numerical counter of this CAR iteration.
 * $classes - HTML class names to be associated with this CAR element.
 * $title - The CAR case consisting of Plaintiff versus. Defendent.
 * $importance - Rank assigned to this CAR.
 * $court - Appellate court where this CAR was decided.
 * 
 */

 $searchDomain = "https://cdm17027.contentdm.oclc.org";
 $searchUrl = "digital/search/searchterm";
 $externalLink = $searchDomain . "/" . $searchUrl . "/" . $car->getA_number();
?>

<div class="car-container <?php print $classes; ?>">

		
	<?php /* include car-admin.tpl.php */ ?>



	<div class="meta car-source">


		<div class="car-item title">
			<?php print $title; ?>
		</div>

		<div class="car-item decision-date">
				<i class="fa-solid fa-calendar-day">&nbsp;</i><?php print $date; ?>
		</div>
		
		<div class="car-item subjects">
			<i class="fa-solid fa-square-check"></i>&nbsp;
			<span class="car-subject meta-highlight">
				<?php print $subject; ?>
			</span>
			&bull;
			<span class="car-secondary-subject meta-highlight">
				<?php print $secondary_subject; ?>
			</span>
		</div>



		<div class="car-item car-rank" title='<?php print "Importance: $importance"; ?>'>
			<?php for($i = 1; $i <= $rank; $i++): ?>
				<i class="fa-solid fa-star"></i>
			<?php endfor; ?>

			<?php for(; $i <= 5; $i++): ?>
				<i class="fa-regular fa-star"></i>
			<?php endfor; ?>
		</div>

		<div class="car-item court">
			<?php print "<h3>$court</h3>"; ?>
		</div>

	</div>
	



	<div class="car-summary">
		<?php print $summary;  ?>
	</div>



	<div class="additional-info car-meta">
		<?php require "admin.tpl.php"; ?>
		<span>
			<?php if(!empty($car->getExternalLink())): ?>
				Links: <a href="<?php print $externalLink; ?>" target="_blank">SOLL</a>
			<?php endif; ?>
			<?php if(is_admin_user() && !empty($car->getUrl())): ?>
				<a href="<?php print $car->getUrl(); ?>" target="_blank">LOD</a>
			<?php endif; ?>
		</span>

		<span>
			<strong>Appellate &pound;: </strong>
			<?php print !empty($car->getA_number()) ? $car->getA_number() : "Not available"; ?>
		</span>

		<span>
			<strong>Citation: </strong>
			<?php print $car->getCitation(); ?>
		</span>
		
		<span>
			<strong>County:</strong>
			<?php print $car->getCounty(); ?>
		</span>
		
		<span>
			<strong>Appellate Judge:</strong>
			<?php print $car->getAppellateJudge(); ?>
		</span>
		
		<span>
			<strong>Trial Judge:</strong>
			<?php print $car->getTrialJudge(); ?>
		</span>
		

	</div>



</div>