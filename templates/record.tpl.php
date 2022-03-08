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

?>

<div class="car-container <?php print $classes; ?>">

		
	<?php /* include car-admin.tpl.php */ ?>


	<div class="meta car-source">
		<div class="car-item title">
			<?php print $title; ?>
		</div>

		<div class="car-item car-rank">
			<?php print "Importance: $importance"; ?>
			<span class="fa fa-star checked"></span>
		</div>

		<div class="car-item court">
			<?php print "<h3>$court</h3>"; ?>
		</div>
		
		<div class="car-item decision-date">
			<?php print $car->getDate(); ?>
		</div>

		<div class="car-item subject-1">
			<?php print strtoupper($car->getSubject()) . " &bull; " . $car->getSubject2(); ?>
		</div>
	</div>
	



	<div class="car-summary">
		<?php /* print nl2br($car->getSummary()); */ ?>
		<?php print $car->getSummary(); ?>
	</div>



	<div class="additional-info car-meta">

		<span>
			<strong>Appellate #: </strong>
			<?php print !empty($car->getA_number()) ? $car->getA_number() : "Not available"; ?>
		</span>

		<span>
			<strong>Citation: </strong>
			<?php print $car->getCitation(); ?>
		</span>
		
		<span>
			<strong>County:</strong>
			<?php print $car->getCircuit(); ?>
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

	<?php if(!empty($car->getExternalLink())) : ?>
		<a href="<?php print $car->getExternalLink(); ?>" target="_blank">View on State of Oregon Law Library</a>
	<?php endif; ?>

	<br />
	
	<?php if(!empty($car->getUrl()) && false && $user->isAdmin()) : ?>
		<a href="<?php print $car->getUrl(); ?>" target="_blank">View on the Library of Defense</a>
	<?php endif; ?>

</div>