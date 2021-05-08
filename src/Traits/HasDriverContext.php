<?php namespace Pehape\Traits;

use Pehape\Helpers\File;

trait HasDriverContext
{

    /**
     * Driver namespace path
     * @var string
     */
    protected $driver_namespace;

    /**
     * Driver directory path
     * @var string
     */
    protected $driver_dir;

    /**
     * Driver initiate function callback name
     * @var string
     */
    protected $driver_callback;

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
          $context = $this->driver_namespace . "\\$method";
          if (! is_a($this->instance, $context) && class_exists($context)) {
            $method = $this->driver_callback;
            $this->instance = new $context;
            return $this->instance;
          }
        }

        // Continue to parent chain
        return parent::__callContext($method);
    }

    /**
     * Use by magic function __call to retrieve method context
     *
     * @param string $value
     * @return bool
     */
    protected function DriverExists($value)
    {
        /** Check drivers dir existance **/
        if ($this->driver_dir && file_exists($this->driver_dir)) {
          /** Fetch all available drivers **/
          $drivers = glob($this->driver_dir . "/*.php", GLOB_BRACE);
          foreach ($drivers as $driver) {
            if ($value == File::BaseName($driver)) {
              return true;
            }
          }
        }

        return false;
    }

}
