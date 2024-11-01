<?php

/*
 * Plugin Name: The Blackest Box's Composer
 * Plugin URI: http://wordpress.org/extend/plugins/tbb-composer/
 * Description: A frontend for the Composer package manager to search and install packages.
 * Author: Sebastian Krüger
 * Version: 0.1.1
 * Author URI: http://theblackestbox.net
 * License: GPL2+
 * Text Domain: tbb-composer
 * Domain Path: /languages/
 */
define('TBB_COMPOSER_VERSION','0.1.1');
define('TBB_COMPOSER_PLUGIN_BASENAME',plugin_basename(__FILE__));
define('TBB_COMPOSER_PLUGIN_LOADER',__FILE__);

require_once dirname( __FILE__ ) . '/class.tbb-composer.php';

new TBBComposer();