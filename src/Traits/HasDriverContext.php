<?php namespace Pehape\Traits;

use Pehape\Helpers\File;

trait HasDriverContext
{

    /**
     * Driver namespace path
     * @var string
     */
    protected $namespace;

    /**
     * Driver directory path
     * @var string
     */
    protected $directory;

    /**
     * Use by magic function __call to retrieve method context
     *
     * @param string $value
     * @return bool
     */
    protected function DriverExists($value)
    {
        /** Check drivers dir existance **/
        if ($this->directory && file_exists($this->directory)) {
          /** Fetch all available drivers **/
          $drivers = glob($this->directory . "/*.php", GLOB_BRACE);
          foreach ($drivers as $driver) {
            if ($value == File::BaseName($driver)) {
              return true;
            }
          }
        }

        return false;
    }

    /**
     * Use by magic function __call to retrieve method context
     *
     * @param string $method
     * @return mixed
     */
    protected function __callContext(&$method)
    {
        /** Check driver class existance **/
        if ($this->DriverExists($method)) {
          $context = $this->namespace . "\\$method";
          if (! is_a($this->instance, $context) && class_exists($context)) {
            $this->instance = new $context;
            return $this->instance;
          }
        }

        // Continue to parent chain
        return parent::__callContext($method);
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

        /** Check if context equal to instance **/
        if ($this->instance === $context) {
          return $context;
        }

        // Trigger intended function
        $result = call_user_func_array(array($context, $method), $args);
        return static::__trigger("On" . ucfirst($method), $args, $result);
    }

}
