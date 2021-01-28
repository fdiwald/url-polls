<?php
namespace Url_Polls;

use Collator;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use const Url_Polls\LANG_DOMAIN;
use const Url_Polls\SETTING_DEFAULT_RECIPIENTS;

/**
 * The model class handling the options for polls
 */
class Polls_Model
{
	/**
	 * Delivers an array with all recipients for the given post_ID.
	 * @since	1.0.0
	 */
	public static function get_recipients_data(int $post_ID, int $items_per_page = 5, int $page_number = 1)
	{
		$data = get_post_meta($post_ID, META_RECIPIENTS);
		if($data != null)
		{
			$data = $data[0];
			$data = json_decode($data, true);
		}
		else
		{
			$data = [];
		}

		return $data;
	}

	/**
	 * Delivers an array with all recipients for the given post_ID including descriptions for the answers.
	 * @since	1.0.0
	 */
	public static function get_recipients_data_decorated(int $post_ID, int $items_per_page = 5, int $page_number = 1)
	{
		$recipients_data = self::get_recipients_data($post_ID, $items_per_page, $page_number);
		if($recipients_data != null)
		{
			$recipients_data = self::decorate_recipients_data($recipients_data);
		}
		return $recipients_data;
	}


	/**
	 * Delivers the list of recipients as excel format.
	 * @since	1.0.0
	 */
	public static function get_recipients_data_excel($post_ID)
	{
		$post = get_post($post_ID);
		$recipients_data = self::get_recipients_data_decorated($post_ID, 0);
		$excel_data = implode("\t",
			[
				__('Name', LANG_DOMAIN),
				__('Status', LANG_DOMAIN),
				__('Accept-URL', LANG_DOMAIN),
				__('Reject-URL', LANG_DOMAIN)
			]
		)."\n";

		foreach($recipients_data as $recipient) { 
			$excel_data .= $recipient['recipient_name'] . "\t";
			$excel_data .= $recipient['answer_description'] . "\t";
			$recipient_ID = urlencode_base64($recipient['recipient_ID']);
			$excel_data .= get_site_url(null, '?' . POST_TYPE_POLL . "=$post->post_name&recipient_ID=$recipient_ID&answer=1") . "\t";
			$excel_data .= get_site_url(null, '?' . POST_TYPE_POLL . "=$post->post_name&recipient_ID=$recipient_ID&answer=2") . "\t\n";
		}

		return $excel_data;
	}


	/**
	 * Supplies optional data in recipients data.
	 * @since	1.0.0
	 */
	public static function decorate_recipients_data($recipients_data)
	{
		array_walk(
			$recipients_data,
			function(&$recipient, $index)
			{
				switch ($recipient['recipient_answer']) {
					case ANSWER_ACCEPT:
						$recipient['answer_description'] = __('accepted', LANG_DOMAIN);
						break;
					case ANSWER_REJECT:
						$recipient['answer_description'] = __('rejected', LANG_DOMAIN);
						break;
					default:
						$recipient['answer_description'] = __('unanswered', LANG_DOMAIN);
						break;
				}
			}
		);
		return $recipients_data;
	}

	/**
	 * Removes optional data in recipients data.
	 * @since	1.0.0
	 */
	public static function undecorate_recipients_data($recipients_data)
	{
		array_walk(
			$recipients_data,
			function(&$recipient, $index)
			{
				unset($recipient['answer_description']);
			}
		);
		return $recipients_data;
	}

	/**
	 * Sorts the recipients by the given column in the given direction.
	 * The sorting is done on the given array. No array copy will be returned.
	 * @since	1.0.1
	 */
	public static function sort_recipients_data(array &$recipients_data, string $sort_column, int $sort_direction)
	{
		if($sort_column != '' && $recipients_data != null)
		{
			usort(
				$recipients_data,
				function($left_recipient, $right_recipient) use ($sort_column, $sort_direction)
				{
					return strnatcmp($left_recipient[$sort_column], $right_recipient[$sort_column]) * $sort_direction;
				}
			);
		}
	}

	/**
	 * Deletes the recipient of the given post
	 * @since	1.0.0
	 */
	public static function delete_recipient($post_ID, $recipient_ID)
	{
		self::delete_recipients($post_ID, [$recipient_ID]);
	}

	/**
	 * Deletes the given recipients of the given post
	 * @since	1.0.0
	 */
	public static function delete_recipients($post_ID, $delete_IDs)
	{
		$recipients_data = self::get_recipients_data($post_ID, 0);
		// Filter out recipients whose ID is contained in delete_ID

		$recipients_data = array_filter(
			$recipients_data,
			function($recipient) use($delete_IDs)
			{
				return !in_array($recipient['recipient_ID'], $delete_IDs);
			}
		);

		self::save_recipients($post_ID, $recipients_data);
	}

	/**
	 * Returns the count of recipients for the given post.
	 * @since	1.0.0
	 */
	public static function get_recipients_count($post_ID)
	{
		$recipients_data = self::get_recipients_data($post_ID, 0);
		return count($recipients_data);
	}

	/**
	 * Saves the recipients data
	 * @since	1.0.0
	 * @return	bool true if the action was successful
	 */
	public static function save_recipients(int $post_ID, $recipients_data)
	{
		$recipients_data = self::undecorate_recipients_data($recipients_data);
		$recipients_data = json_encode($recipients_data);
		return update_post_meta($post_ID, META_RECIPIENTS, $recipients_data);
	}

	/**
	 * Adds the default recipients from the options menu into the given poll.
	 * @since	1.0.0
	 */
	public static function add_default_recipients($post_ID)
	{
		$recipients_data = self::get_recipients_data($post_ID, 0);
		$existing_recipient_names = array_map(function($element){return $element['recipient_name'];}, $recipients_data);
		$default_recipients = array_filter(explode("\n", get_option(SETTING_DEFAULT_RECIPIENTS, '')));

		// Only add recipients that are not already in the poll
		foreach($default_recipients as $recipient_name)
		{
			$recipient_name = preg_replace('/[\r\n\t]/', '', $recipient_name);
			if(!in_array($recipient_name, $existing_recipient_names))
			{
				$recipients_data[] = [
					'recipient_name' => $recipient_name,
					'recipient_answer' => 0,
					'recipient_ID' => base64_encode(random_bytes(16))
				];
			}
		}

		self::save_recipients($post_ID, $recipients_data);
	}

	/**
	 * Saves the given answer to the poll.
	 * @since	1.0.0
	 * @return	bool true if the action was successful
	 */
	public static function save_answer(int $post_ID, string $recipient_ID, int $answer_ID)
	{
		return self::bulk_answer($post_ID, [$recipient_ID], $answer_ID);
	}

	/**
	 * Sets the answers of multiple recipients
	 * @since	1.0.0
	 */
	public static function bulk_answer(int $post_ID, array $recipient_IDs, int $answer_ID)
	{
		$recipients_data = self::get_recipients_data($post_ID, 0);
		$recipient_found = false;
		foreach($recipients_data as &$recipient)
		{
			if(in_array($recipient['recipient_ID'], $recipient_IDs))
			{
				$recipient['recipient_answer'] = $answer_ID;
				$recipient_found = true;
			}
		}

		if($recipient_found)
		{
			if(self::save_recipients($post_ID, $recipients_data))
			{
				return $answer_ID;
			}
		}
		return false;
	}
}