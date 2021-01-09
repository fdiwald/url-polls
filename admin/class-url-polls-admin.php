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
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url_polls    The ID of this plugin.
	 */
	private $url_polls;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $url_polls       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $url_polls, $version ) {

		$this->url_polls = $url_polls;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->url_polls, plugin_dir_url( __FILE__ ) . 'css/url-polls-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->url_polls, plugin_dir_url( __FILE__ ) . 'js/url-polls-admin.js', array( 'jquery' ), $this->version, false );

	}

}
