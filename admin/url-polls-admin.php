<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/admin
 */

namespace Url_Polls\Admin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Url_Polls\Admin\Polls\Polls_Controller;
use const Url_Polls\LANG_DOMAIN;
use const Url_Polls\MENU_SETTINGS;
use const Url_Polls\PLUGIN_NAME;
use const Url_Polls\POST_TYPE_POLL;
use const Url_Polls\SETTING_DEFAULT_RECIPIENTS;
use const Url_Polls\SETTINGS_SECTION_GENERAL;
use const Url_Polls\VERSION;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/admin
 * @author     Your Name <email@example.com>
 */
class Url_Polls_Admin {
	/**
	 * The controller for the polls menu entry
	 * @since	1.0.0
	 */
	var Polls_Controller $polls_controller;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $url_polls       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct(\Url_Polls_Loader $loader)
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/polls/polls-controller.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/polls/polls-view.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/polls/recipients-list.php';
		
		$this->pollsController = new Polls\Polls_Controller();

		$loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
		$loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
		$loader->add_action('admin_menu', $this, 'setup_menu');
		$loader->add_action('admin_init', $this, 'setup_settings');
		$loader->add_action('add_meta_boxes', $this, 'add_meta_boxes');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Url_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Url_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'css/url-polls-admin.css', array(), VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Url_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Url_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'js/url-polls-admin.js', array( 'jquery' ), VERSION, false );
	}

	/**
	 * Adds the menu pages to the admin menu.
	 * @since	1.0.0
	 */
	public function setup_menu()
	{
		add_submenu_page(
			'edit.php?post_type='.POST_TYPE_POLL,
			__('URL Polls page title', LANG_DOMAIN),
			__('Settings', LANG_DOMAIN),
			'manage_options',
			MENU_SETTINGS,
			array($this, 'render_settings_page')
		);
	}

	/**
	 * Registers settings for the plugin
	 * @since	1.0.0
	 */
	public function setup_settings()
	{
		register_setting(SETTINGS_SECTION_GENERAL, SETTING_DEFAULT_RECIPIENTS, [
			'type' => 'string',
			'description' => __('A list of person\'s names separated by line breaks which are added as recipients to new polls.', LANG_DOMAIN),
			'sanitize_callback' => 'sanitize_textarea_field',
			'show_in_rest' => false,
			'default' => '']
		);
		add_settings_section(
			SETTINGS_SECTION_GENERAL,
			__('URL Polls General settings', LANG_DOMAIN),
			'',
			MENU_SETTINGS
		);
		add_settings_field(
			SETTING_DEFAULT_RECIPIENTS,
			__('Default recipients', LANG_DOMAIN),
			[$this, 'render_default_recipient_setting'],
			MENU_SETTINGS,
			SETTINGS_SECTION_GENERAL
		);
	}

	/**
	 * Renders the settings page.
	 * @since	1.0.0
	 */
	public function render_settings_page()
	{
		echo '<form action="options.php" method="post">';
		settings_fields(SETTINGS_SECTION_GENERAL);
		do_settings_sections(MENU_SETTINGS);
		submit_button(__('Submit', LANG_DOMAIN));
		echo '</form>';
	}
	public function render_default_recipient_setting($args)
	{
		$default_recipients = get_option(SETTING_DEFAULT_RECIPIENTS);
		echo '<textarea id="'.SETTING_DEFAULT_RECIPIENTS.'" name="'.SETTING_DEFAULT_RECIPIENTS."\" rows=\"20\" cols=\"40\">$default_recipients</textarea>";
	}

	/**
	 * Registers the metaboxes
	 */
	public function add_meta_boxes()
	{
		add_meta_box('url-polls_poll_recipients_metabox',
		__('Recipients', LANG_DOMAIN),
		array($this->pollsController, 'render_recipients_metabox'));
	}
}
