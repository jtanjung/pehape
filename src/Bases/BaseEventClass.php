<?php namespace Pehape\Bases;

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
    protected static $event_listener = array();

    /**
     * Exception instance class name
     * @var string
     */
    protected $exception = "\\RuntimeException";

    /**
     * Determine whether exception will be thrown by the __saveCall method
     * @var bool
     */
    protected $silent_mode = true;

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

        $listener = explode(' ', $key);
        foreach ($listener as $val) {
          $name = trim($val);
          if ($name) {
            static::$event_listener[$name] = $value;
          }
        }

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
        if (@static::$event_listener[$key]) {
            unset(static::$event_listener[$key]);
        }
        return $this;
    }

    /**
     * Set silent mode
     *
     * @return self
     */
    public function Silent(bool $value)
    {
        $this->silent_mode = $value;
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
    protected static function __trigger($method, $args = array(), $result = null)
    {
        $event_listener = @static::$event_listener[$method];
        $global_event = false;

        if (! $event_listener) {
            $event_listener = @static::$event_listener['OnEvent'];
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

            if ($result !== null) {
              array_unshift($arguments, $result);
            }

            $event_result = call_user_func_array($event_listener, $arguments);

            if (! $return && $event_result) {
                $return = $event_result;
            }
        }

        return $return;
    }

    /**
     * Activate/deactivate global event listener "OnEvent"
     *
     * @param bool $value
     * @return self
     */
    public function Listener(bool $value)
    {
        if ($this->instance instanceof BaseEventClass) {
          $this->instance->Unbind('OnEvent');
          if ($value) {
            $this->instance->Bind('OnEvent', static::__events);
          }
        }

        return $this;
    }

    /**
     * Used as gateway for the global event listener "OnEvent" trigger by
     * the "instance" object. This function should be used in child class
     * to receive "instance" notification, then child class will trigger
     * intended method to end interface event listener.
     *
     * @return mixed
     */
    protected static function __events()
    {
        $arguments = func_get_args();
        $method = array_pop($arguments);
        return static::__trigger($method, $arguments);
    }
    /**
     * Magic function to set property
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
          $this->$key = $value;
        }
        if (is_object($this->instance)) {
          $this->instance->$key = $value;
        }
    }

    /**
     * Magic function to get property
     *
     * @param string $key
     * @return mixed
     */
    public function &__get($key)
    {
        $result = null;
        try {
          $result = property_exists($this, $key) ? $this->$key : @$this->instance->$key;
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * Use to execute a function inside exception handler try catch
     *
     * @param function $method
     * @param mixed $return
     * @param array $parameters
     * @return self
     */
    protected function __safeCall($method, &$return = null, array $parameters = [])
    {
        // Initiate return value
        $result = false;

        try {

          // Execute the method
          $return = call_user_func_array($method, $parameters);
          $result = true;

        } catch (\Exception $e) {

          // Trigger exception
          $exception = $this->exception;
          $exception = new $exception($e->getMessage());
          // Check if silent mode is disabled
          if (! $this->silent_mode) {
            // Throw the exception
            throw $exception;
          }
          else {
            // Include the exception as a parameter, so it can be thrown
            // by user function/listener 'OnError'
            static::__trigger('OnError', [$e->getMessage(), $exception]);
          }
        }

        return $result;
    }

    /**
     * Use by magic function __call to retrieve method context
     *
     * @param string $method
     * @return mixed
     */
    protected function __callContext(&$method)
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
        $this->__safeCall(array($context, $method), $result, $args);
        return static::__trigger("On" . ucfirst($method), $args, $result);
    }
}
