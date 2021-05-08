<?php namespace Pehape\Bases;

use Pehape\Bases\BaseClass;
use Pehape\Bases\BaseConfig;
use Pehape\Constants\Message;

/**
 * Class BaseEventClass
 * @package Pehape\Bases
 */
abstract class BaseEventClass extends BaseClass
{

    /**
     * Child instance object
     * @var Object
     */
    protected $instance;

    /**
     * List of callable event listener
     * @var array
     */
    protected $event_listener = array();

    /**
     * Bind callback event for each method call
     *
     * @param string $key
     * @param function or mixed $value
     * @return self
     */
    public function Bind($key, $value)
    {
        if (! is_string($key) || ! is_callable($value)) {
            throw new \InvalidArgumentException(Message::$NOT_EVENT);
        }

        $this->event_listener[ $key ] = $value;
        return $this;
    }

    /**
     * Unbind event listener
     *
     * @param string $key
     * @return self
     */
    public function Unbind($key)
    {
        if (@$this->event_listener[ $key ]) {
            unset($this->event_listener[ $key ]);
        }
        return $this;
    }

    /**
     * Trigger event listener called by magic function __call
     *
     * @param string $method
     * @param array $args
     * @param object $context
     * @param mixed $result
     * @return mixed
     */
    protected function __trigger($method, $args = array(), $result = null)
    {
        $event_listener = @$this->event_listener[ $method ];
        $global_event = false;

        if (! $event_listener) {
            $event_listener = @$this->event_listener[ 'OnEvent' ];
            $global_event = true;
        }

        $return = $result;

        if (is_callable($event_listener)) {
            $arguments = $args;
            if (! is_array($arguments)) {
                $arguments = array($arguments);
            }

            if ($global_event) {
                array_push($arguments, $method);
            }

            array_unshift($arguments, $result);

            $event_result = call_user_func_array($event_listener, $arguments);

            if (! $return && $event_result) {
                $return = $event_result;
            }
        }

        return $return;
    }

    /**
     * Use by magic function __call to retrieve method context
     *
     * @param string $method
     * @return mixed
     */
    protected function __callContext($method)
    {
        if (method_exists($this, $method)) {
          return $this;
        }

        if( is_object($this->instance) ) {
          return method_exists($this->instance, $method) ? $this->instance : $this;
      	}

        throw new \RuntimeException(
            sprintf(Message::$NO_METHOD, get_class($this), $method)
        );
    }

    /**
     * Magic function to call method
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args = array())
    {
        $context = $this->__callContext($method);
        $result = call_user_func_array(array($context, $method), $args);
        return $this->__trigger("On" . ucfirst($method), $args, $result);
    }
}
