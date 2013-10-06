<?php

class BootstrapComponent extends Object 
{
/**
 * Flash element type aliases
 * 
 * @var array
 */
	private $aliases = array(
		'default' => 'success',
		'notice'  => 'info',
		'warn'    => 'warning',
		'error'   => 'danger'
	);

/**
 * {@inheritdoc}
 */
	public function initialize(Controller &$controller, $settings = array()) {}

/**
 * {@inheritdoc}
 */
	public function startup(Controller &$controller) {}

/**
 * {@inheritdoc}
 */
	public function beforeRender(Controller &$controller)
	{
		$this->replaceFlashMessage();
	}

/**
 * {@inheritdoc}
 */
	public function shutdown(Controller &$controller) {}

/**
 * {@inheritdoc}
 */
	public function beforeRedirect(Controller &$controller, $url, $status = null, $exit = true)
	{
		$this->replaceFlashMessage();
		return parent::beforeRedirect($controller, $url, $status, $exit);
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
		if (!$flash = CakeSession::read('Message.flash'))
		{
			return;
		}

		if (in_array($flash['element'], array('default', 'success', 'error', 'notice', 'info', 'warning'))) {
			$flash = array_replace_recursive($flash, array(
				'element' => 'flash',
				'params'  => array(
					'class'  => $this->getFlashClassName($flash['element']),
					'plugin' => 'Bootstrap'
				)
			));
			CakeSession::write('Message.flash', $flash);
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
