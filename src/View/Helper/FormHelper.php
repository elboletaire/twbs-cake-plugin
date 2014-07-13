<?php
namespace Bootstrap\View\Helper;

use Cake\Core\App;
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
		parent::__construct($View, $config);

		$form_templates = App::path('Config', 'Bootstrap');
		$form_templates = realpath(array_pop($form_templates) . 'forms.php');
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
	public function button($title, array $options = array())
	{
		$options += ['class' => 'btn-default'];
		if (!isset($options['btnClass']) || (isset($options['btnClass']) && $options['btnClass'])) {
			// always add .btn class
			$options = $this->addClass($options, 'btn');
		}
		return parent::button($title, $options);
	}

/**
 * {@inheritdoc}
 */
	// We need to directly modify checkboxFormGroup in order to have labels wrapping checkboxes
	public function formatTemplate($name, $data)
	{
		switch ($name) {
			case 'checkboxFormGroup':
				$data['input'] = preg_replace('/(<label(?:[^>]+)>)([^<]+)(<\/label>)/i', "$1${data['input']} $2$3", $data['label']);
				unset($data['label']);
			break;
		}
		return parent::formatTemplate($name, $data);
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
 * {@inheritdoc}
 */
	// Add classes to labels
	public function label($fieldName, $text = null, array $options = [])
	{
		$options = $this->addClass($options, 'control-label');
		return parent::label($fieldName, $text, $options);
	}
}
