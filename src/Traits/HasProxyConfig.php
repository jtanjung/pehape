<?php namespace Pehape\Traits;

use Pehape\Configs\ProxyConfig;
use Pehape\Helpers\Objects;

trait HasProxyConfig
{

    /**
     * Proxy config
     * @var ProxyConfig
     */
    public $Proxy;

    /**
     * Set Proxy configurations
     *
     * @param string $ip
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $type
     * @return self
     */
    public function SetProxy()
    {
        $arguments = func_get_args();
        $ip = $arguments[0];
        /** Check if first parameter is an instance of ProxyConfig **/
        if ($ip instanceof ProxyConfig) {
          $this->Proxy = $ip;
          return $this;
        }

        /** Check if first parameter is an array **/
        if (is_array($ip) && !Objects::IsSequentialIndexed($ip)) {
          $this->Proxy = new ProxyConfig($ip);
          return $this;
        }

        $port = (int)$arguments[1];
        $username = $password = false;
        $type = 'HTTP';

        $count = func_num_args();
        if($count > 2){
          if($count == 3){
            $type = $arguments[2];
          }
          else {
            $username = $arguments[3];
            $password = @$arguments[4];
          }
        }

        $this->Proxy = new ProxyConfig([
          'IP' => $ip,
          'Port' => $port,
          'UserName' => $username,
          'Password' => $password,
          'Type' => $type
        ]);

        return $this;
    }

    /**
     * Get the proxy information from ProxyConfig.
     * If proxy config is not present, then try to retrieve the information
     * from user-function callback by calling "OnProxy" trigger
     *
     * @return ProxyConfig
     */
    public function GetProxy()
    {
        $value = $this->Proxy instanceof ProxyConfig ? $this->Proxy : static::__trigger("OnProxy", [$this]);
        /** Convert array value into ProxyConfig **/
        if (is_array($value) && !Objects::IsSequentialIndexed($value)) {
          $value = new ProxyConfig($value);
        }
        else {
          $value = false;
        }

        return $value;
    }

}
