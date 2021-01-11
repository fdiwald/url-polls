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

	/**
	 * Adds the menu pages to the admin menu.
	 * @since	1.0.0
	 */
	public function setup_menu()
	{
		$pollsController = new Url_Polls\Admin\Polls\Polls_Controller();
		add_menu_page(__('URL Polls page title'),
						__('URL Polls'),
						'manage_options',
						'url-polls',
						array($pollsController, 'render_options_page'),
						plugin_dir_url( __DIR__ ) . 'icon.png'
					);
	}

	/**
	 * Registers the required post_types.
	 * @since	1.0.0
	 */
	public function register_post_types()
	{
		register_post_type('url-polls_poll',
							array(
								'label' => __('URL Polls'),
								'labels' => array(
									'name' => __('URL Polls'),
									'singular_name' => __('URL Poll'),
									'add_new' => __('Add Poll'),
									'edit_item' => __('Edit Poll'),
									'new_item' => __('New Poll'),
									'view_item' => __('View Poll'),
									'view_items' => __('View Polls'),
									'search_items' => __('Search Polls'),
									'not_found' => __('No Polls found'),
									'not_found_in_trash' => __('No Polls found in trash'),
									'all_items' => __('All Polls'),
									'insert_into_item' => __('Insert into Poll'),
									'filter_items_list' => __('Filter Polls list'),
									'item_published' => __('Poll published.'),
									'item_reverted_to_draft' => __('Poll reverted to draft.'),
									'item_updated' => __('Poll updated.')
								),
								'description' => 'A poll awaiting feedback by URLs',
								'public' => true,
								'exclude_from_search' => true,
								'publicly_queryable' => false,
								'show_in_menu' => true,
								'show_in_nav_menus' => false,
								'menu_icon' => plugin_dir_url( __DIR__ ) . 'icon.png',
								'supports' => array('title')
							)
						);
	}
}
