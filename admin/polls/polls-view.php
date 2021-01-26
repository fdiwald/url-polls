<?php
namespace Url_Polls\Admin\Polls;

use const Url_Polls\ACTION_ADD_DEFAULT_RECIPIENTS;
use const Url_Polls\ACTION_EXPORT;
use const Url_Polls\LANG_DOMAIN;

/**
 * The view class rendering the options for polls
 */
class Polls_View
{
	public function render_recipients_metabox(Recipients_List $recipients_list, $post_id)
	{
		?>
		<div class="wrap">
			<?php
			$add_default_url = add_query_arg('action', ACTION_ADD_DEFAULT_RECIPIENTS);
			$add_default_url = wp_nonce_url($add_default_url, ACTION_ADD_DEFAULT_RECIPIENTS);
			echo '<a href="'.$add_default_url.'">'.__('Add default recipients', LANG_DOMAIN).'</a>';
			$export_url = add_query_arg('action', ACTION_EXPORT);
			$export_url = wp_nonce_url($export_url, ACTION_EXPORT);
			echo ' <a href="'.$export_url.'" id="export-to-excel">'.__('Export', LANG_DOMAIN).'</a>';
			$recipients_list->display();
			?>
		</div>
		<?php
	}
}
