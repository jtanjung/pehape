<?php namespace Pehape\Factories;

use Pehape\Bases\BaseClass;
use Pehape\Constants\Message;

/**
 * Class AccountFactory
 * @package Pehape\Factories
 */
class ProxyFactory extends BaseClass
{

    /**
     * Proxy provider to be used
     * @var BaseProxyDriver
     */
    protected $driver;

    /**
     * Proxy count limit
     * @var int
     */
    protected $limit;

    /**
     * Set and initialize account driver
     *
     * @param string $value
     * @return self
     */
    public function SetDriver(string $value)
    {
        /** Initialize default properties **/
        $this->driver = false;
        $this->driver_name = strtoupper($value);
        /** Create driver object based on the $value **/
        $driver = "\\Pehape\\Drivers\\Accounts\\" . $this->driver_name . 'Driver';
        /** Check account driver class existance **/
        if(class_exists($driver)){
          /** Create driver object **/
          $this->driver = new $driver;
          return $this;
        }

        /** Throw error if account driver is not present **/
        throw new \RuntimeException(
            sprintf(Message::$NO_CLASS, $driver)
        );
    }

    /**
     * Set accounts info which is ned to be generated
     *
     * @param array|int $value
     * @return self
     */
    public function SetAccounts($value)
    {
        $this->accounts = is_array($value) || is_int($value) ? $value : false;
        return $this;
    }

    /**
     * Execute accounts creation
     *
     * @return self
     */
    public function Execute()
    {
        /** Empty the bucket **/
        $this->buckets = [];

        /** Set account instance **/
        $instance = "\\Pehape\\Models\\" . $this->driver_name . 'Account';
        /** Check for accounts value existance **/
        if(! $this->accounts || ! class_exists($instance)){
          return $this;
        }

        $accounts = $this->accounts;

        /** Generate appropriate accounts list based on int value **/
        if( is_int($accounts) ){
          /** Create account iinstances list **/
          $accounts = [];
          $counter = $this->accounts;
          while($counter > 0){
            $accounts[] = new $instance;
            $counter--;
          }
        }

        /** Convert single account instance into array **/
        if (is_a($accounts, $instance)) {
          $accounts = [$accounts];
        }

        /** Register accounts list **/
        foreach($accounts as $account){
          /** Verify account constructor **/
          if (!is_a($account, $instance)) {
            continue;
          }
          /** Create new account entry **/
          $result = $this->driver->Create($account);
          /** Check if account creation succeed **/
          if (is_a($result, $instance)) {
            //** Push a new account into the bucket **/
            $this->buckets[] = $result;
          }
        }

        return $this;
    }

}
