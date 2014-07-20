<?php
namespace Bootstrap\Controller\Component;

use Cake\Event\Event;
use Cake\Controller;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\SessionComponent;

/**
 * @author  Òscar Casajuana <elboletaire@underave.net>
 */
class BootstrapComponent extends Component
{
	public $components = ['Session'];
/**
 * Flash element type aliases
 *
 * @var array
 */
	private $aliases = array(
		'Flash/success' => 'success',
		'Flash/notice'  => 'info',
		'Flash/warn'    => 'warning',
		'Flash/warning' => 'warning',
		'Flash/error'   => 'danger',
		'Flash/danger'  => 'danger'
	);

/**
 * {@inheritdoc}
 */
	public function beforeRender(Event $event)
	{
		$this->replaceFlashMessage();
	}

/**
 * Replace flash message
 *
 * Replaces the flash message with the bootstraped one. It takes the
 * $element param as the flash message class.
 *
 * @return void
 */
	private function replaceFlashMessage()
	{
		if (!$flash = $this->Session->read('Flash.flash')) {
			return;
		}

		if (in_array($flash['element'], array_keys($this->aliases))) {
			$flash = array_replace_recursive($flash, array(
				'element' => 'Bootstrap.flash',
				'params'  => array(
					'class'  => $this->getFlashClassName($flash['element'])
				)
			));
			$this->Session->write('Flash.flash', $flash);
		}
	}

/**
 * Returns the flash class name depending on it's alias
 *
 * @param  string $name The alias name.
 * @return string       Equivalent class name. If it does not exist will return $name.
 */
	private function getFlashClassName($name)
	{
		if (array_key_exists($name, $this->aliases)) {
			return $this->aliases[$name];
		}
		return $name;
	}
}
