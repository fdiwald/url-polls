<?php
//,,,,,, https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
namespace Url_Polls\Admin\Polls;

use WP_List_Table;

use const Url_Polls\ACTION_BULK_ACCEPT;
use const Url_Polls\ACTION_BULK_DELETE;
use const Url_Polls\ACTION_BULK_REJECT;
use const Url_Polls\ACTION_DELETE_RECIPIENT;
use const Url_Polls\LANG_DOMAIN;

/**
 * The class for rendering a list of recipients for a poll
 * @since	1.0.0
 */
class Recipients_List extends WP_List_Table
{
	public function __construct()
	{
		parent::__construct([
			'singular' => __('Recipient', LANG_DOMAIN),
			'plural' => __('Recipients', LANG_DOMAIN),
			'ajax' => false
		]);
	}

	/** Text displayed when no recipient data is available */
	public function no_items()
	{
		_e('No recipients avaliable.', LANG_DOMAIN);
	}

	/**
	 * Get the columns and their titles
	 * @since	1.0.0
	 */
	public function get_columns()
	{
		return ['cb' => '<input type="checkbox" />',
				'recipient_name' => __('Name', LANG_DOMAIN),
				'answer_description' => __('Answer', LANG_DOMAIN),
				'recipient_ID' => __('ID', LANG_DOMAIN)];
	}

	/**
	 * Method for name column
	 *
	 * @since	1.0.0
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_recipient_name($item)
	{
	
		$title = '<strong>' . $item['recipient_name'] . '</strong>';
		
		$delete_url = add_query_arg(['post' => $_REQUEST['post'], 'action' => ACTION_DELETE_RECIPIENT, 'recipient_ID' => urlencode($item['recipient_ID'])]);
		$delete_url = wp_nonce_url($delete_url, ACTION_DELETE_RECIPIENT);
		$actions = [
			'delete' => "<a href=\"$delete_url\">Delete</a>"
		];
	
		return $title . $this->row_actions($actions);
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @since	1.0.0
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name)
	{
		switch ($column_name)
		{
			case 'answer_description':
			case 'recipient_ID':
				return $item[$column_name];
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
		'<input type="checkbox" name="bulk-recipient_IDs[]" value="%s" />', $item['recipient_ID']
		);
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'recipient_name' => array( 'recipient_name', true ),
			'recipient_answer' => array( 'recipient_answer', true )
		);
	
		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return [
			ACTION_BULK_DELETE => __('Delete', LANG_DOMAIN),
			ACTION_BULK_ACCEPT => __('Accept', LANG_DOMAIN),
			ACTION_BULK_REJECT => __('Reject', LANG_DOMAIN)
		];
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items()
	{
		$this->_column_headers = $this->get_column_info();
	
		$per_page = $this->get_items_per_page('recipients_per_page', 0);
		$page_number = $this->get_pagenum();
		$total_items = count($this->items);
	
		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page //WE have to determine how many items to show on a page
		]);
	}

	/**
	 * Generates the table navigation above or below the table
	 * Had to overwrite the base function to leave out the nonce
	 * @since 1.0.0
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( $this->has_items() ) : ?>
			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
				<?php
			endif;
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear" />
		</div>
		<?php
	}
	/**
	 * Displays the bulk actions dropdown.
	 * Had to overwrite the base function to get "actions2" instead of "actions" for the name of the select.
	 * @since 1.0.0
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backward compatibility.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();

			/**
			 * Filters the items in the bulk actions menu of the list table.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen.
			 *
			 * @since 3.1.0
			 * @since 5.6.0 A bulk action can now contain an array of options in order to create an optgroup.
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action2" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk actions' ) . "</option>\n";

		foreach ( $this->_actions as $key => $value ) {
			if ( is_array( $value ) ) {
				echo "\t" . '<optgroup label="' . esc_attr( $key ) . '">' . "\n";

				foreach ( $value as $name => $title ) {
					$class = ( 'edit' === $name ) ? ' class="hide-if-no-js"' : '';

					echo "\t\t" . '<option value="' . esc_attr( $name ) . '"' . $class . '>' . $title . "</option>\n";
				}
				echo "\t" . "</optgroup>\n";
			} else {
				$class = ( 'edit' === $key ) ? ' class="hide-if-no-js"' : '';

				echo "\t" . '<option value="' . esc_attr( $key ) . '"' . $class . '>' . $value . "</option>\n";
			}
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction2" ) );
		echo "\n";
	}
}