<?php
/*
	Plugin Name: WP Browsersync Reload
	Plugin URI:  https://zach-adams.com
	Description:
	Version:     1.0
	Author:      Zach Adams
	Author URI:  https://zach-adams.com
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Class WP_Browsersync_Reload
 *
 * @since 1.0.0
 */
class WP_Browsersync_Reload {

	/**
	 * init
	 *
	 * Initialize the plugin and add our hooks
	 *
	 * @type    function
	 * @since   1.0.0
	 */
	public static function init() {
		$self = new self();
		add_action('save_post', array($self, 'reload_browsersync'));
	}

	public function reload_browsersync() {
		$args = ['blocking' => false];
		wp_remote_get('http://192.168.1.2:3000/__browser_sync__?method=reload', $args);
	}
}

//Hook the plugin launch after plugins have loaded
add_action('plugins_loaded', array('WP_Browsersync_Reload', 'init'));