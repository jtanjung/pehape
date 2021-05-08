<?php
namespace Pehape\Models;

use Pehape\Bases\BaseStdClass;
use Pehape\Helpers\Objects;

/**
 * Class Option
 * @package Pehape\Models
 */
class Option extends BaseStdClass {

    /**
     * Magic function to set property
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (is_array($value)) {
          if (! Objects::IsSequentialIndexed($value)) {
            $this->_properties[$key] = new self($value);
          }
          else {
            $this->_properties[$key] = $value;
          }
        }
        else {
          $this->_properties[$key] = $value;
        }
    }

}
