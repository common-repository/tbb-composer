<div class="wrap">

        <h2><?php _e('Installed Packages',TBB_COMPOSER_SLUG) ?> <a href="<?php echo get_admin_url() . 'admin.php?page=' . TBB_COMPOSER_SLUG . '_update' ?>" class="add-new-h2"><?php _e('Update Project',TBB_COMPOSER_SLUG) ?></a></h2>
		<?php echo $messages ?>		
        <form id="" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php if(isset($table)) $table->display() ?>
        </form>
    </div>