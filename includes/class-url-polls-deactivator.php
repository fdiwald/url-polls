<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/includes
 */

use const Url_Polls\POST_TYPE_POLL;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Url_Polls
 * @subpackage Url_Polls/includes
 * @author     Your Name <email@example.com>
 */
class Url_Polls_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear the permalinks to remove our post type's rules from the database.
		unregister_post_type(POST_TYPE_POLL);
		flush_rewrite_rules();
	}

}
