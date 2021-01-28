<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use const Url_Polls\LANG_DOMAIN;
use const Url_Polls\POST_TYPE_POLL;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Url_Polls
 * @subpackage Url_Polls/includes
 * @author     Your Name <email@example.com>
 */

class Url_Polls {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Url_Polls_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();

		if(is_admin())
		{
			// The admin controller registers the required hooks itself.
			new \Url_Polls\Admin\Url_Polls_Admin($this->loader);
		}
		else
		{
			$this->define_public_hooks();
		}
		$this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Url_Polls_Loader. Orchestrates the hooks of the plugin.
	 * - Url_Polls_i18n. Defines internationalization functionality.
	 * - Url_Polls_Admin. Defines all hooks for the admin area.
	 * - Url_Polls_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-url-polls-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-url-polls-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/polls-model.php';

		if(is_admin()) 
			$this->load_admin_dependencies();
		else
			$this->load_public_dependencies();

		$this->loader = new Url_Polls_Loader();
	}

	/**
	 * Loads dependencies for admin pages
	 * @since	1.0.0
	 */
	private function load_public_dependencies()
	{
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/url-polls-public.php';
	}

	/**
	 * Loads dependencies for admin pages
	 * @since	1.0.0
	 */
	private function load_admin_dependencies()
	{
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/url-polls-admin.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Url_Polls_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Url_Polls_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Url_Polls_Public();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks needed in admin and public
	 * @since	1.0.0
	 */
	private function define_hooks()
	{
		$this->loader->add_action('init', $this, 'register_post_types');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Url_Polls_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Registers the required post_types.
	 * @since	1.0.0
	 */
	public static function register_post_types()
	{
		register_post_type(
			POST_TYPE_POLL,
			array(
				'labels' => array(
					'name' => __('URL Polls', LANG_DOMAIN),
					'singular_name' => __('URL Poll', LANG_DOMAIN),
					'add_new' => __('Add Poll', LANG_DOMAIN),
					'edit_item' => __('Edit Poll', LANG_DOMAIN),
					'new_item' => __('New Poll', LANG_DOMAIN),
					'view_item' => __('View Poll', LANG_DOMAIN),
					'view_items' => __('View Polls', LANG_DOMAIN),
					'search_items' => __('Search Polls'), LANG_DOMAIN,
					'not_found' => __('No Polls found', LANG_DOMAIN),
					'not_found_in_trash' => __('No Polls found in trash', LANG_DOMAIN),
					'all_items' => __('All Polls', LANG_DOMAIN),
					'insert_into_item' => __('Insert into Poll', LANG_DOMAIN),
					'filter_items_list' => __('Filter Polls list', LANG_DOMAIN),
					'item_published' => __('Poll published.', LANG_DOMAIN),
					'item_reverted_to_draft' => __('Poll reverted to draft.', LANG_DOMAIN),
					'item_updated' => __('Poll updated.', LANG_DOMAIN)
				),
				'description' => __('A poll awaiting feedback by URLs', LANG_DOMAIN),
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'menu_icon' => plugin_dir_url( __DIR__ ) . 'admin/icon.png',
				'supports' => array('title'),
				'has_archive' => true,
				'rewrite' => false
			)
		);
	}
}

