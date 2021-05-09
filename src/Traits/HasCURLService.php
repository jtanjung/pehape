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
        return $this->Get($value);
    }

}
