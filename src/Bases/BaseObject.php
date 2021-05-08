<?php namespace Pehape\Bases;

/**
 * Class BaseObject
 * @package Pehape\Bases
 */
abstract class BaseObject
{

    /**
     * Keep property default value on construction
     * @var boolean
     */
    private $__default_value;

    /**
     * Class constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = null)
    {
        $this->__default_value = true;
        $this->Set($attributes);
    }

    /**
     * Set class attribute
     *
     * @param object or array
     * @return self
     */
    public function Set($attributes)
    {
        $this->__reset();

        if (! is_array($attributes) &&
              ! is_object($attributes)) {
            return $this;
        }

        foreach ($attributes as $key => $value) {
            $this->{ $key } = $value;
        }

        return $this;
    }

    /**
     * Reset attribute
     *
     * @return void
     */
    protected function __reset()
    {
        $object = new \ReflectionClass($this);
        $properties = $object->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($this->__default_value && ! is_null($this->{ $property->name })) {
                continue;
            }

            $this->{ $property->name } = null;
        }

        $this->__default_value = false;
    }
}
