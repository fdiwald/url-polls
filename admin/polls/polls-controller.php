<?php
namespace Url_Polls\Admin\Polls;

/**
 * The controller class handling the options for polls
 */
final class Polls_Controller extends \Url_Polls\Admin\MVC\Base_Controller
{
	protected function get_model_instance() {return new Polls_Model();}

	public function render_options_page()
	{
		if (!current_user_can('manage_options')) return;
		
		require plugin_dir_path(__FILE__) . 'polls-view.php';
	}
}