
<?php ?>

<div style="text-align:center;">
    <label>
        <h3>
            <?php print $message; ?>
        </h3>
    </label>
</div>

<?php if($user->isAdmin()) : ?>
    <div>
        </br></br>
        <h3><?php print $query; ?></h3>
    </div>
<?php endif; ?>