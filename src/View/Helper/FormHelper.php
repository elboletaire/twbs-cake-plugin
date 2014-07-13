<?php
namespace Bootstrap\View\Helper;

use Cake\Core\App;
use Cake\View\View;
use Cake\View\Helper;

/**
 * Simple helper for using lessc with cakephp
 * @author Òscar Casajuana <elboletaire@underave.net>
 * @version 3.0.0
 */
class FormHelper extends Helper\FormHelper
{
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

	public function button($title, array $options = array())
	{
		$options += ['class' => 'btn btn-default'];
		return parent::button($title, $options);
	}

	public function formatTemplate($name, $data)
	{

		switch ($name) {
			case 'checkboxFormGroup':
				$data['input'] = preg_replace('/(<label for="[a-z]+">)([^<]+)(<\/label>)/i', "$1${data['input']} $2$3", $data['label']);
				unset($data['label']);
				break;
		}
		return parent::formatTemplate($name, $data);
	}
}
