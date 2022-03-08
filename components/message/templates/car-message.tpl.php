

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-messages.css" />

<div class="messages-container">
        <?php print "CAR message-widget says... " . $this->court; ?>

        <?php 
        // var_dump($this);
        // Notice the reference to $this->court!  Use this.


        /*
		$courtMsg = empty($this->court) ? "" : "in $court";

		$month = $month == "All Months" ? null : $month;

		if(!empty($month)) $dateMsg = empty($year) ? "for the month of $month (All Years)" : "for $month";
		if(!empty($day)) $dateMsg .= ", $day";
		if(!empty($year)) $dateMsg .= empty($month) ? "for $year" : ", $year";

		$msg = "";

		if(!empty($subject)) $msg .= "<h3>$subject</h3>";

		$msg .= "showing " . $count . " case review(s)";
		if(!empty($courtMsg)) $msg .= " $courtMsg";
		if(!empty($dateMsg)) $msg .= " $dateMsg";

		if(!empty($county)) $msg .= "<h4>$count decision(s) made in $county County</h4>";
        */

        
        ?>


    <?php if(false && $user->isAdmin()):  ?>
        
        <h3>
            <?php print $this->query; ?>
        </h3>
        
    <?php endif; ?>
</div>