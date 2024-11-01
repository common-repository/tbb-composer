<div class="wrap">

        <h2><?php _e('Search Packages',TBB_COMPOSER_SLUG) ?></h2>
		<?php echo $messages ?>
		<p><?php _e('It may take a while for getting search results, depending on your Composer project configuration.',TBB_COMPOSER_SLUG) ?></p>
		<h4><?php _e('Search',TBB_COMPOSER_SLUG) ?></h4>
		<form id="search-packages" method="get" action="">
		
			<input type="hidden" name="page" value="<?php echo TBB_COMPOSER_SLUG . '_search' ?>">
				<input type="search" name="s" value="" autofocus="autofocus">
				<label class="screen-reader-text" for="package-search-input"><?php _e('Search Packages',TBB_COMPOSER_SLUG) ?></label>
				<input type="submit" name="package-search-input" id="package-search-input" class="button" value="<?php _e('Search Packages') ?>">	</form>
        
    </div>
	