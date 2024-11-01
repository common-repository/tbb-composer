<div class="wrap">

        <h2><?php _e('Search Packages',TBB_COMPOSER_SLUG) ?> <a href="<?php echo get_admin_url() . 'admin.php?page=' . TBB_COMPOSER_SLUG . '_update' ?>" class="add-new-h2"><?php _e('Update Project',TBB_COMPOSER_SLUG) ?></a></h2>
		<?php echo $messages ?>
		<form id="search-packages" method="get" action="">
			<input type="hidden" name="page" value="<?php echo TBB_COMPOSER_SLUG . '_search' ?>">
				<input type="search" name="s" value="<?php echo $_GET['s'] ?>" autofocus="autofocus">
				<label class="screen-reader-text" for="package-search-input"><?php _e('Search Packages',TBB_COMPOSER_SLUG) ?></label>
				<input type="submit" name="package-search-input" id="package-search-input" class="button" value="<?php _e('Search Packages') ?>">	</form>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php if(isset($table)) $table->display() ?>
        </form>
        
    </div>