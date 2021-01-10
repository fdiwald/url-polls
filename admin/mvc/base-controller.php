<?php
namespace Url_Polls\Admin\MVC;
/**
 * The base class for all controllers
 * @since	1.0.0
 */
abstract class Base_Controller
{
	private Base_Model $model;
	private Base_View $view;

	/**
	 * Returns the class name used to instantiate the model
	 * @since	1.0.0
	 */
	abstract protected function get_model_instance();

	/**
	 * The model delivering data
	 * @since	1.0.0
	 */
	public function get_model() : Base_Model
	{
		if(!isset($this->model))
		{
			$class_name = $this->get_model_instance();
			$this->model = new $class_name;
		}
		return $this->model;
	}

	/**
	 * Returns the class name used to instantiate the view
	 * @since	1.0.0
	 */
	abstract protected function get_view_instance();

	/**
	 * The view delivering the GUI
	 * @since	1.0.0
	 */
	public function get_view() : Base_View
	{
		if(!isset($this->view))
		{
			$class_name = $this->get_view_instance();
			$this->view = new $class_name;
		}
		return $this->view;
	}
}