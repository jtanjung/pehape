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
        return $this->Get($value);
    }

}
