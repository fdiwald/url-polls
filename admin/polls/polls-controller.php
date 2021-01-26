<?php
namespace Url_Polls\Admin\Polls;

use Url_Polls\Polls_Model;

use function Url_Polls\sanitize_base64;

use const Url_Polls\ACTION_ADD_DEFAULT_RECIPIENTS;
use const Url_Polls\ACTION_BULK_ACCEPT;
use const Url_Polls\ACTION_BULK_DELETE;
use const Url_Polls\ACTION_BULK_REJECT;
use const Url_Polls\ACTION_DELETE_RECIPIENT;
use const Url_Polls\ACTION_EXPORT;
use const Url_Polls\ANSWER_ACCEPT;
use const Url_Polls\ANSWER_REJECT;
use const Url_Polls\POST_TYPE_POLL;

/**
 * The controller class handling the options for polls
 * @since	1.0.0
 */
final class Polls_Controller
{
	private Recipients_List $recipients_list;

	/**
	 * @since	1.0.0
	 */
	public function __construct()
	{
		add_action('admin_init', [$this, 'create_recipients_list']);
		add_action('admin_action_'.ACTION_DELETE_RECIPIENT, [$this, 'delete_recipient']);
		add_action('admin_action_editpost', [$this, 'run_bulk_action']);
		add_action('admin_action_'.ACTION_ADD_DEFAULT_RECIPIENTS, [$this, 'add_default_recipients']);
		add_action('admin_action_'.ACTION_EXPORT, [$this, 'export']);
	}

	/**
	 * Initializes the Recipients_List.
	 * @since	1.0.0
	 */
	public function create_recipients_list()
	{
		$this->recipients_list = new Recipients_List();
	}

	/**
	 * Gathers the recipients data for the given poll and renders it for placement in a metabox
	 * @since	1.0.0
	 */
	public function render_recipients_metabox($post)
	{
		$view = new Polls_View();
		$this->recipients_list->items = Polls_Model::get_recipients_data_decorated($post->ID, 0);
		$this->recipients_list->prepare_items();
		$view->render_recipients_metabox($this->recipients_list, $post->ID);
	}

	/**
	 * Deletes the recipient
	 * @since	1.0.0
	 */
	public function delete_recipient()
	{
		$post_ID = absint($_REQUEST['post']);
		// In our file that handles the request, verify the nonce.
		$nonce = esc_attr($_REQUEST['_wpnonce']);
	
		if (!wp_verify_nonce($nonce, ACTION_DELETE_RECIPIENT))
		{
			die('nonce check failed');
		}
		else {
			Polls_Model::delete_recipient($post_ID, sanitize_base64(urldecode($_REQUEST['recipient_ID'])));
			
			wp_redirect(add_query_arg(['post' => $post_ID, 'action' => 'edit'], 'post.php'));
			exit;
		}
	}

	/**
	 * Deletes multiple recipients
	 * @since	1.0.0
	 */
	public function delete_recipients(int $post_ID, array $recipient_IDs)
	{
		Polls_Model::delete_recipients($post_ID, $recipient_IDs);
		exit;
	}

	/**
	 * Add default recipients
	 * @since	1.0.0
	 */
	public function add_default_recipients()
	{
		$post_ID = absint($_REQUEST['post']);
		// In our file that handles the request, verify the nonce.
		$nonce = esc_attr($_REQUEST['_wpnonce']);

		if (!wp_verify_nonce($nonce, ACTION_ADD_DEFAULT_RECIPIENTS))
		{
			die('nonce check failed');
		}
		Polls_Model::add_default_recipients($post_ID);
		
		wp_redirect(add_query_arg(['post' => $post_ID, 'action' => 'edit'], 'post.php'));
		exit;
	}

	/**
	 * Export the list of recipients with their answers and answer links to Excel.
	 * @since	1.0.0
	 */
	public function export()
	{
		$post_ID = absint($_REQUEST['post']);
		// In our file that handles the request, verify the nonce.
		$nonce = esc_attr($_REQUEST['_wpnonce']);

		if (!wp_verify_nonce($nonce, ACTION_EXPORT))
		{
			die('nonce check failed');
		}
		$excel_data = Polls_Model::get_recipients_data_excel($post_ID);
		
		if($excel_data)
		{
			header('Content-Disposition: attachment; filename="export.xlsx"');
			header('Content-Type: application/vnd.ms-excel'); 
			echo $excel_data;
		}
		else
		{
			wp_redirect(add_query_arg(['post' => $post_ID, 'action' => 'edit'], 'post.php'));
		}
		exit;
	}

	/**
	 * Handles bulk actions for the recipients table
	 * @since	1.0.0
	 */
	public function run_bulk_action()
	{
		$post_ID = absint($_REQUEST['post_ID']);
		if (get_post_type($post_ID) == POST_TYPE_POLL)
		{
			$nonce = esc_attr($_REQUEST['_wpnonce']);
			if (!wp_verify_nonce($nonce, "update-post_$post_ID"))
			{
				die('nonce check failed');
			}		
			
			$action = $this->get_action();
			$recipient_IDs = esc_sql($_REQUEST['bulk-recipient_IDs']);		
			switch($action)
			{
				case ACTION_BULK_DELETE:
					Polls_Model::delete_recipients($post_ID, $recipient_IDs);
					break;
				case ACTION_BULK_ACCEPT:
					Polls_Model::bulk_answer($post_ID, $recipient_IDs, ANSWER_ACCEPT);
					break;
				case ACTION_BULK_REJECT:
					Polls_Model::bulk_answer($post_ID, $recipient_IDs, ANSWER_REJECT);
					break;
			}

			wp_redirect(add_query_arg(['post' => $post_ID, 'action' => 'edit'], 'post.php'));
		}
	}

	/**
	 * Returns the action associated with the request
	 * @since	1.0.0
	 */
	private function get_action()
	{
		// Bulk actions
		if (isset($_REQUEST['action2']))
		{
			if(is_array($_REQUEST['action2']))
			{
				// Multiple drop downs for bulk actions -> take the first with an actual value
				foreach($_REQUEST['action2'] as $action)
				{
					if($action != '-1') return $action;
				}
			}
			else
			{
				return $_REQUEST['action2'];
			}
		}

		// Standard actions
		if (isset($_REQUEST['action']))
		{
			return $_REQUEST['action'];
		}

		// No action
		return '';
	}
}