<?php namespace Pehape\Traits;

trait HasCURLService
{

    /**
     * Start request using curl
     *
     * @param string $value
     * @return self
     */
    protected function Load($value)
    {
        // Notify event listener about process preparation
        static::__trigger('OnLoading');
        $this->instance->SetUrl($value);
        $this->instance->Execute();
        return $this;
    }

}
