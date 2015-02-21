<?php
/**
 * FormHelper replacement to add Twitter Bootstrap classes to cakePHP forms
 *
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @license MIT
 * @copyright Òscar Casajuana 2013-2015
 */
namespace Bootstrap\View\Helper;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
use Cake\View\Helper;

class FormHelper extends Helper\FormHelper
{
	/**
	 * {@inheritdoc}
	 *
	 * Load custom templates
	 *
	 * @param \Cake\View\View $View The View this helper is being attached to.
	 * @param array $config Configuration settings for the helper.
	 */
	public function __construct(View $View, array $config = [])
	{
		// Merge parent FormHelper defaults with this FormHelper ones
		$this->_defaultConfig = array_merge([
			'input_class'     => 'form-control',
			'button_class'    => 'btn-default',
			'force_class_btn' => true // after enabled can be disabled on runtime,
									  // passing `'btnClass' => false`
		], $this->_defaultConfig);

		parent::__construct($View, $config);

		$form_templates = Plugin::path('Bootstrap') . 'config' . DS;
		$form_templates = realpath($form_templates . 'forms.php');
		$form_templates = function() use ($form_templates) {
			require $form_templates;
			return $config;
		};

		$this->templates($form_templates());
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add .btn and .btn-default classes to buttons
	 *
	 * @var string $title
	 * @var array  $options
	 */
	public function button($title, array $options = [])
	{
		$options += ['class' => $this->config('button_class')];
		$is_btn_enabled = !isset($options['btnClass']) || (isset($options['btnClass']) && $options['btnClass']);
		// always add .btn class
		if ($this->config('force_class_btn') && $is_btn_enabled) {
			$options = $this->addClass($options, 'btn');
		}
		return parent::button($title, $options);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add .form-control class to datetime selects
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array $options Array of Options
	 * @return string Generated set of select boxes for time formats chosen.
	 * @see Cake\View\Helper\FormHelper::dateTime() for templating options.
	 */
	public function date($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options, ['year', 'month', 'day']);
		return parent::date($fieldName, $options);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add .form-control class to datetime selects
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array $options Array of Options
	 * @return string Generated set of select boxes for time formats chosen.
	 * @see Cake\View\Helper\FormHelper::dateTime() for templating options.
	 */
	public function datetime($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options);
		return parent::datetime($fieldName, $options);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add default input class to datetime selects
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array $options Array of Options
	 * @return string Generated set of select boxes for time formats chosen.
	 * @see Cake\View\Helper\FormHelper::dateTime() for templating options.
	 */
	public function time($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options, ['hour', 'minute']);
		return parent::time($fieldName, $options);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add default input class to inputs generated using the
	 * magic __call method
	 *
	 * @param string $method Method name / input type to make.
	 * @param array $params Parameters for the method call
	 * @return string Formatted input method.
	 * @throws \Cake\Core\Exception\Exception When there are no params for the method call.
	 */
	public function __call($method, $params)
	{
		if (empty($params)) {
			throw new Error\Exception(sprintf('Missing field name for FormHelper::%s', $method));
		}
		$class = [
			'class' => $this->config('input_class')
		];
		if (isset($params[1])) {
			$params[1] += $class;
		} else {
			$params[1] = $class;
		}
		return parent::__call($method, $params);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Add classes to inputs
	 *
	 * @param string $fieldName the field name
	 * @param array $options The options for the input element
	 * @return string The generated input element
	 */
	protected function _getInput($fieldName, $options)
	{
		if (in_array($options['type'], array('select', 'url', 'text', 'textarea'))) {
			foreach (explode(' ', $this->config('input_class')) as $class) {
				$options = $this->addClass($options, $class);
			}
		}
		return parent::_getInput($fieldName, $options);
	}

	/**
	 * Adds the .form-control class to every datetime select
	 *
	 * @param array $options
	 * @param array $periods
	 */
	private function addDatetimeClasses(array &$options = [], array $periods = ['year', 'month', 'day', 'hour', 'minute'])
	{
		$class = ['class' => $this->config('input_class')];
		foreach ($periods as $period) {
			if (empty($options[$period])) {
				$options[$period] = [];
			}
			$options[$period] += $class;
		}
	}
}
