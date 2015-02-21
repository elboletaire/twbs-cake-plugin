<?php
/**
 * FlashComponent replacement to style flash messages
 * as twitter bootstrap alerts
 *
 * @author  Òscar Casajuana <elboletaire@underave.net>
 * @license MIT
 * @copyright Òscar Casajuana 2013-2015
 */
namespace Bootstrap\Controller\Component;

use Cake\Event\Event;
use Cake\Controller\Component;

class FlashComponent extends Component\FlashComponent
{
    /**
     * Flash element type aliases
     *
     * @var array
     */
    private $aliases = array(
        'Flash/success' => 'success',
        'Flash/notice'  => 'info',
        'Flash/warn'    => 'warning',
        'Flash/error'   => 'danger'
    );

    /**
     * {@inheritdoc}
     *
     * Replaces the default FlashComponent::set
     * behavior to use twitter bootstrap alerts.
     * All them use the same `flash` layout.
     *
     * @param string|\Exception $message Message to be flashed. If an instance
     *   of \Exception the exception message will be used and code will be set
     *   in params.
     * @param array $options An array of options
     * @return void
     */
    public function set($message, array $options = [])
    {
        parent::set($message, $options);
        $options += $this->config();
        $flash = $this->_session->read("Flash.{$options['key']}");
        if (array_key_exists($flash['element'], $this->aliases) || in_array(str_replace('Flash/', null, $flash['element']), $this->aliases)) {
            $flash = array_replace_recursive($flash, array(
                'element' => 'Bootstrap.flash',
                'class'  => $this->getFlashClassName($flash['element'])

            ));
            $this->_session->write("Flash.{$options['key']}", $flash);
        }
    }

    /**
     * Returns the flash class name depending on its alias
     *
     * @param  string $name The alias name.
     * @return string       Equivalent class name. If it does not exist will return $name.
     */
    private function getFlashClassName($name)
    {
        if (array_key_exists($name, $this->aliases)) {
            return $this->aliases[$name];
        }
        return str_replace('Flash/', null, $name);
    }
}
