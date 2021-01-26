<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/public
 */

use Url_Polls\Polls_Model;

use function Url_Polls\sanitize_base64;
use function Url_Polls\urldecode_base64;

use const Url_Polls\ANSWER_ACCEPT;
use const Url_Polls\ANSWER_REJECT;
use const Url_Polls\PLUGIN_NAME;
use const Url_Polls\POST_TYPE_POLL;
use const Url_Polls\VERSION;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Url_Polls
 * @subpackage Url_Polls/public
 * @author     Your Name <email@example.com>
 */
class Url_Polls_Public {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		add_filter('the_content', [$this, 'handle_answer']);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style(PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'css/url-polls-public.css', array(), VERSION, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script(PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'js/url-polls-public.js', array( 'jquery' ), VERSION, false );

	}

	/**
	 * Checks, if the request is an answer to a poll and saves it.
	 * @since	1.0.0
	 */
	public function handle_answer($content)
	{
		// Is this a request to a poll?
		if(!get_queried_object()) return $content;
		if(get_queried_object()->post_type != POST_TYPE_POLL) return $content;
		$post_ID = get_queried_object()->ID;
		if(!$post_ID > 0) return $content;
		
		$answer_ID = $this->get_answer_ID();
		$recipient_ID = $this->get_recipient_ID();
		if($answer_ID && $recipient_ID)
		{
			$result = Polls_Model::save_answer($post_ID, $recipient_ID, $answer_ID);
			switch ($result) {
				case ANSWER_ACCEPT:
					$content = __('You successfully accepted the date suggestion.');
					break;

				case ANSWER_REJECT:
					$content = __('You successfully rejected the date suggestion.');
					break;
					
				default:
					$content = __('There has been an error saving your answer.');
					break;
			}
		}
		else
		{
			// If it is no answer request, redirect to the home page.
			wp_redirect('index.php');
		}
		return $content;
	}

	/**
	 * Returns the answer_ID from the request.
	 * @since	1.0.0
	 */
	private function get_answer_ID()
	{
		if(!isset($_REQUEST['answer'])) return false;

		$answer_ID = absint($_REQUEST['answer']);
		if(!($answer_ID >= 0)) return false;
		
		return $answer_ID;
	}

	/**
	 * Returns the answer_ID from the request.
	 * @since	1.0.0
	 */
	private function get_recipient_ID()
	{
		if(!isset($_REQUEST['recipient_ID'])) return false;

		$recipient_ID = $_REQUEST['recipient_ID'];
		$recipient_ID = urldecode_base64($recipient_ID);
		$recipient_ID = sanitize_base64($recipient_ID);
		if($recipient_ID == '') return false;
		
		return $recipient_ID;
	}

	/**
	 * Saves the answer in the poll.
	 * @since	1.0.0
	 */
	private function save_answer()
	{
		$post_ID = get_queried_object()->ID;
		$recipient_ID = sanitize_base64(urldecode($_REQUEST['recipient_ID']));
		$answer_ID = absint($_REQUEST['answer']);

		Polls_Model::save_answer($post_ID, $recipient_ID, $answer_ID);
	}
}
