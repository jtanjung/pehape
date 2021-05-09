<?php namespace Pehape\Bases;

use Pehape\Helpers\Objects;

/**
 * Class BaseStdClass
 * @package Pehape\Bases
 */
class BaseStdClass implements \JsonSerializable, \IteratorAggregate
{

    /**
     * List of property values
     * @var array
     */
    protected $_properties = [];

    /**
     * Class constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = null)
    {
        $this->Set($attributes);
    }

    /**
     * Set class attribute
     *
     * @param object|array $value
     * @return self
     */
    public function Set($value)
    {
        $attributes = $value;
        if (! is_array($attributes) &&
              ! is_object($attributes)) {
            return $this;
        }
        if (is_object($attributes)) {
            $attributes = Objects::JSONToArray($attributes);
        }
        $this->_properties = array_merge($this->_properties, $attributes);
        return $this;
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
        $this->_properties[$key] = $value;
    }

    /**
     * Magic function to get property
     *
     * @param string $key
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->_properties[$key];
    }

    /**
     * Magic function to check if property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_properties[$key]);
    }

    /**
         * Serializes the object to a value that can be serialized natively by json_encode()
         *
         * @return array
         */
    public function jsonSerialize()
    {
        return $this->_properties;
    }

    /**
         *  Retrieve an external iterator
         *
         * @return ArrayIterator
         */
    public function getIterator()
    {
        return new \ArrayIterator(Objects::ArrayToJSON($this->_properties));
    }

    /**
     * Count the properties
     *
     * @return int
     */
    public function count()
    {
        return count($this->_properties);
    }

    /**
     * Check if property is not empty
     *
     * @return boolean
     */
    public function is_empty()
    {
        return ($this->count() == 0);
    }

    /**
     * Empty the properties
     *
     * @return void
     */
    public function empty()
    {
        $this->_properties = [];
    }

    /**
     * Get a random property
     *
     * @return mixed
     */
    public function random()
    {
        $this->_properties = [];
        $keys = array_keys($this->_properties);
        $key = array_rand($keys);
        return $this->_properties[$key];
    }

}
