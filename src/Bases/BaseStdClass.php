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

        foreach ($attributes as $key => $value) {
          $this->$key = $value;
        }

        return $this;
    }

    /**
     * Magic function to set property
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->_properties[$key] = $value;
        if (is_array($value)) {
          if (!Objects::IsSequentialIndexed($value)) {
            $this->_properties[$key] = new static($value);
          }
          else {
            $data = $this->_properties[$key];
            foreach ($data as $k => $v) {
              if (is_array($v) && !Objects::IsSequentialIndexed($v)) {
                $data[$k] = new static($v);
                continue;
              }
              $data[$k] = $v;
            }
            $this->_properties[$key] = $data;
          }
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
     * Serializes the object to a value that can be
     * serialized natively by json_encode()
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
     * Load properties from XML file.
     *
     * @param string $filename
     * @return self
     */
    public function loadXML(string $filename)
    {
        $properties = simplexml_load_file($filename);
        $this->Set($properties)->initLoad();
        return $this;
    }

    /**
     * Load properties from JSON file.
     *
     * @param string $filename
     * @return self
     */
    public function loadJSON(string $filename)
    {
        $properties = file_get_contents($filename);
        $properties = json_decode($properties, true);
        $this->Set($properties)->initLoad();
        return $this;
    }

    /**
     * Dummy function to initialize the properties after it
     * has been loaded from a file.
     *
     * @return self
     */
    protected function initLoad()
    {
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
        // Check for properties exsistance
        if ($this->_properties) {
          $keys = array_keys($this->_properties);
          $key = $keys[array_rand($keys)];
          return $this->_properties[$key];
        }
    }

}
