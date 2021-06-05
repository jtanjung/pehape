<?php namespace Pehape\Traits;

use Pehape\Constants\WebDriver;

trait HasWebPageService
{

    /**
     * Web driver name
     * @var string
     */
    protected $browser;

    /**
     * Start web page loading using a random browser
     *
     * @param string $value
     * @return self
     */
    protected function Load($value = null)
    {
        // Notify event listener about process preparation
        static::__trigger('OnLoading');

        /** Choose browser randomly **/
        $randoms = [WebDriver::$chrome, WebDriver::$firefox];
        $browser = is_string($this->browser) ? $this->browser : $randoms[array_rand($randoms)];
        /** Create the connection **/
        $this->instance->$browser()->Create();
        /** Check url value paramater existance **/
        if (is_string($value)) {
          $this->instance->get($value);
        }
        return $this;
    }

}
