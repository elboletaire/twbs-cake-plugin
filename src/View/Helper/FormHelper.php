<?php
namespace Bootstrap\View\Helper;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
use Cake\View\Helper;

/**
 * Simple FormHelper replacement to add Twitter Bootstrap classes to our forms
 *
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @version 3.0.0
 */
class FormHelper extends Helper\FormHelper
{
/**
 * {@inheritdoc}
 */
	// Load custom templates
	public function __construct(View $View, array $config = [])
	{
		// Force templateClass
		$config['templateClass'] = 'Bootstrap\View\StringTemplate';

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
 */
	// Add btn and btn-default classes to buttons
	public function button($title, array $options = [])
	{
		$options += ['class' => 'btn-default'];
		if (!isset($options['btnClass']) || (isset($options['btnClass']) && $options['btnClass'])) {
			// always add .btn class
			$options = $this->addClass($options, 'btn');
		}
		return parent::button($title, $options);
	}

	public function date($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options, ['year', 'month', 'day']);
		return parent::date($fieldName, $options);
	}

	public function datetime($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options);
		return parent::datetime($fieldName, $options);
	}

	public function time($fieldName, array $options = [])
	{
		$this->addDatetimeClasses($options, ['hour', 'minute']);
		return parent::time($fieldName, $options);
	}

/**
 * {@inheritdoc}
 */
	public function __call($method, $params)
	{
		if (empty($params)) {
			throw new Error\Exception(sprintf('Missing field name for FormHelper::%s', $method));
		}
		$class = [
			'class' => 'form-control'
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
 */
	// Add classes to inputs
	protected function _getInput($fieldName, $options)
	{
		if (in_array($options['type'], array('select', 'url', 'text', 'textarea'))) {
			$options = $this->addClass($options, 'form-control');
		}
		return parent::_getInput($fieldName, $options);
	}

	/**
	 * Adds the .form-control class to every datetime select
	 *
	 * @param array $options
	 */
	private function addDatetimeClasses(array &$options = [], $periods = ['year', 'month', 'day', 'hour', 'minute'])
	{
		$class = ['class' => 'form-control'];
		foreach ($periods as $period) {
			if (empty($options[$period])) {
				$options[$period] = [];
			}
			$options[$period] += $class;
		}
	}
}
