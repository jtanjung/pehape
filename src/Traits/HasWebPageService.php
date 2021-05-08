<?php namespace Pehape\Traits;

use Pehape\Configs\AuthConfig;

trait HasWebPageService
{

    /**
     * Start web page loading using a random browser
     *
     * @param string $value
     * @return self
     */
    protected function Load($value = null)
    {
        /** Choose browser randomly **/
        $randoms = ['Chrome', 'FireFox'];
        $browser = $randoms[array_rand($randoms)];
        /** Create the connection **/
        $this->instance->$browser()->Create();
        /** Check url value paramater existance **/
        if (is_string($value)) {
          $this->instance->get($value);
        }
        return $this;
    }

}
