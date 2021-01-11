<?php
namespace Url_Polls\Admin\MVC;
/**
 * The base class for all controllers
 * @since	1.0.0
 */
abstract class Base_Controller
{
	private Base_Model $model;

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
}