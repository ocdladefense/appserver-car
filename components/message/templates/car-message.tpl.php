

<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/car-messages.css" />

<div class="messages-container">
    <h3>
        <?php 
        // var_dump($this);
        print $this->getMessage(); ?>
    </h3>

    <?php if(false && $user->isAdmin()):  ?>
        
        <h3>
            <?php print $query; ?>
        </h3>
        
    <?php endif; ?>
</div>