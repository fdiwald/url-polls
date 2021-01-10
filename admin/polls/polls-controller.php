<?php
namespace Url_Polls\Admin\Polls;

/**
 * The controller class handling the options for polls
 */
final class Polls_Controller extends \Url_Polls\Admin\MVC\Base_Controller
{
	protected function get_model_instance() {return new Polls_Model();}
	protected function get_view_instance() {return new Polls_View();}

	public function render_options_page()
	{
		$this->get_view()->render("");
	}
}