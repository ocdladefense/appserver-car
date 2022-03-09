<?php
/**
 * @template message
 * 
 * Display summary of the executed query.
 */
?>


<div class="messages-container">

    <?php if(false && $user->isAdmin()):  ?>
        
        <div class="query">
            <?php print $query; ?>
        </h3>
        
    <?php endif; ?>
</div>