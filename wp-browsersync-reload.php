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
defined( 'ABSPATH' ) or die();

define( 'WP_BROWSERSYNC_RELOAD_SLUG', 'wp_browsersync_reload_' );

/**
 * Class WP_Browsersync_Reload
 *
 * @since 1.0.0
 */
class WP_Browsersync_Reload {

	/**
	 * Contains the users options
	 * @var array
	 */
	private $options;

	public function __contruct( $extra_hooks ) {

		/*
		 * Load our options
		 */
		$this->options = get_option(WP_BROWSERSYNC_RELOAD_SLUG . 'options');

	}

	/**
	 * init
	 *
	 * Initialize the plugin and add our hooks
	 *
	 * @type    function
	 * @since   1.0.0
	 */
	public function init() {

		add_action('save_post', array($this, 'reload_browsersync'));
		
	}

	public function reload_browsersync() {
		$args = ['blocking' => false];
		wp_remote_get('http://192.168.1.2:3000/__browser_sync__?method=reload', $args);
	}
}

//TODO: Add a hook for others to easily add which other hooks they want to attach browsersync to
$extra_hooks = array();

$wp_browsersync = new WP_Browsersync_Reload();
add_action('init', array($wp_browsersync, 'init'));