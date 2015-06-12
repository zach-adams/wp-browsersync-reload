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

define( 'WP_BROWSERSYNC_RELOAD', 'wp_browsersync_reload' );
define( 'WP_BROWSERSYNC_RELOAD_SLUG', WP_BROWSERSYNC_RELOAD . '_' );

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

	/**
	 * Shows whether the plugin is enabled or not
	 * @var bool
	 */
	public $enabled;


	/**
	 * __contruct
	 *
	 * Loads the plugin options and admin pages
	 *
	 * @type    function
	 * @since
	 * @param * @param $extra_hooks
	 */
	public function __construct( $extra_hooks ) {

		/*
		 * Load our options
		 */
		$this->options = get_option(WP_BROWSERSYNC_RELOAD_SLUG . 'settings');

		/*
		 * Set enabled based on whether the plugin is enabled in the options
		 */
		if(!empty($this->options)) {
			$enable_browsersync_reload = ( is_numeric( $this->options[ 'enable_browsersync_reload' ] ) ? (int) $this->options[ 'enable_browsersync_reload' ] : 0 );
			if ( $enable_browsersync_reload === 1 )
				$this->enabled = true;
		} else {
			$this->enabled = false;
		}

		/*
		 * Load our admin options pages
		 */
		if (is_admin()) {
			add_action( 'admin_menu', array($this, 'add_admin_menu') );
			add_action( 'admin_init', array($this, 'admin_init'));
		}

	}

	/**
	 * add_options_page
	 *
	 * Adds our options page to the admin settings menu
	 *
	 * @type    function
	 * @since   1.0.0
	 */
	public function add_admin_menu() {

		add_options_page(
			'Browsersync Reload',
			'Browsersync Reload',
			'manage_options',
			WP_BROWSERSYNC_RELOAD,
			array($this, 'create_settings_page')
		);

	}

	/**
	 * create_settings_page
	 *
	 * HTML for our settings page
	 *
	 * @type    function
	 * @since   1.0.0
	 */
	public function create_settings_page() {
		?>
		<div class="wrap">
			<h2>Browsersync Reload Settings</h2>
			<form method="post" action="options.php">
				<?php
					settings_fields( WP_BROWSERSYNC_RELOAD_SLUG . 'settings' );
					do_settings_sections( WP_BROWSERSYNC_RELOAD );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * admin_init
	 *
	 * Setup our plugins settings in the admin
	 *
	 * @type    function
	 * @since   1.0.0
	 */
	public function admin_init() {

		register_setting(
			WP_BROWSERSYNC_RELOAD_SLUG . 'settings',
			WP_BROWSERSYNC_RELOAD_SLUG . 'settings',
			array($this, 'sanitize_settings')
		);

		add_settings_section(
			WP_BROWSERSYNC_RELOAD_SLUG . 'main_settings',
			null,
			null,
			WP_BROWSERSYNC_RELOAD
		);

		add_settings_field(
			'enable_browsersync_reload',
			'Enable Browsersync Reload',
			array($this, 'setting_field_callback'),
			WP_BROWSERSYNC_RELOAD,
			WP_BROWSERSYNC_RELOAD_SLUG . 'main_settings',
			'enable_browsersync_reload'
		);

		add_settings_field(
			'browsersync_host',
			'Browsersync Host',
			array($this, 'setting_field_callback'),
			WP_BROWSERSYNC_RELOAD,
			WP_BROWSERSYNC_RELOAD_SLUG . 'main_settings',
			'browsersync_host'
		);

		add_settings_field(
			'browsersync_port',
			'Browsersync Port',
			array($this, 'setting_field_callback'),
			WP_BROWSERSYNC_RELOAD,
			WP_BROWSERSYNC_RELOAD_SLUG . 'main_settings',
			'browsersync_port'
		);

	}

	/**
	 * setting_field_callback
	 *
	 * Create settings
	 *
	 * @type    function
	 * @since
	 * @param * @param $setting
	 */
	public function setting_field_callback( $setting ){

		switch($setting) {
			case 'enable_browsersync_reload':
				$val = false;
				if(!empty($this->options['enable_browsersync_reload']))
					$val = '1';
				echo '<input type="checkbox" id="enable_browsersync_reload" name="'. WP_BROWSERSYNC_RELOAD_SLUG . 'settings[enable_browsersync_reload]" value="1" '. checked(1, $val, false) .' />'  ;
				break;
			case 'browsersync_host':
				$val = 'localhost';
				if(!empty($this->options['browsersync_host']))
					$val = $this->options['browsersync_host'];
				echo '<input type="text" id="browsersync_host" name="'. WP_BROWSERSYNC_RELOAD_SLUG . 'settings[browsersync_host]" value="'. $val .'" />'  ;
				break;
			case 'browsersync_port':
				$val = '3000';
				if(!empty($this->options['browsersync_port']))
					$val = $this->options['browsersync_port'];
				echo '<input type="text" id="browsersync_port" name="'. WP_BROWSERSYNC_RELOAD_SLUG . 'settings[browsersync_port]" value="'. $val .'" />'  ;
				break;
		}

	}

	/**
	 * sanitize_settings
	 *
	 * Sanitize user input
	 *
	 * @type    function
	 * @since   1.0.0
	 * @param   $input The user's input
	 * @return  array The safe, sanitized values for the database
	 */
	public function sanitize_settings( $input ) {

		/**
		 * Our safe return array
		 */
		$safe_input = array();

		/**
		 * Ensure that the checkbox to enable the plugin is either a 1 or 0
		 */
		if(isset($input['enable_browsersync_reload'])) {
			$enable_browsersync_reload = (is_numeric($input['enable_browsersync_reload']) ? (int)$input['enable_browsersync_reload'] : 0);
			if ($enable_browsersync_reload === 1)
				$safe_input['enable_browsersync_reload'] = "1";
			else
				$safe_input['enable_browsersync_reload'] = "0";
		} else {
			$safe_input['enable_browsersync_reload'] = "0";
		}

		/**
		 * Make sure the browsersync host is an IP address or the string "localhost"
		 * TODO: Make it possible to have proxy hosts and xip.io values
		 */
		if(isset($input['browsersync_host']) && (filter_var($input['browsersync_host'], FILTER_VALIDATE_IP) || sanitize_text_field($input['browsersync_host']) === "localhost")) {
			$safe_input['browsersync_host'] = sanitize_text_field($input['browsersync_host']);
		} else {
			wp_die("Please enter a valid IP address for your Browsersync Host");
		}

		/**
		 * Make sure the browsersync port is a valid int and is in the range ports are suppose to be
		 * TODO: If port is not needed (proxy or xip.io) make it optional
		 */
		if(isset($input['browsersync_port']) && filter_var($input['browsersync_port'], FILTER_VALIDATE_INT) && ($input['browsersync_port'] <= 65535) && ($input['browsersync_port'] > 0)) {
			$browsersync_port = filter_var($input['browsersync_port'], FILTER_SANITIZE_NUMBER_INT);
			$safe_input['browsersync_port'] = sanitize_text_field($browsersync_port);
		} else {
			wp_die("Please enter a valid Port Number for your Browsersync Port");
		}

		/**
		 * Return our safe input
		 */
		return $safe_input;

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

	/**
	 * reload_browsersync
	 *
	 * The magic.
	 *
	 * @type    function
	 * @since
	 * @param * @param $post_id
	 */
	public function reload_browsersync( $post_id ) {

		/**
		 * If this is just a post revision or autosave ignore it
		 */
		if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id))
			return;

		/**
		 * The remote request arguments
		 */
		$args = [
			'blocking' => false
		];

		/**
		 * Send the get request to the browsersync HTTP API Protocol
		 */
		$response = wp_remote_get('http://'. $this->options['browsersync_host'] .':'. $this->options['browsersync_port'] .'/__browser_sync__?method=reload', $args);

		/**
		 * If there was an error print it out
		 * TODO: More helpful error messages
		 */
		if(is_wp_error($response)) {
			wp_die('<strong>WP Browsersync Reload Plugin Error</strong>: ' . $response->get_error_message(), 'WP Browsersync Reload Plugin Error');
		}
	}
}

//TODO: Add a hook for others to easily add which other hooks they want to attach browsersync to
$extra_hooks = array();

$wp_browsersync = new WP_Browsersync_Reload( $extra_hooks );

if ($wp_browsersync->enabled === true)
	add_action('init', array($wp_browsersync, 'init'));