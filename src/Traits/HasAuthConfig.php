<?php namespace Pehape\Traits;

use Pehape\Configs\AuthConfig;
use Pehape\Models\Option;
use Pehape\Helpers\Objects;

trait HasAuthConfig
{

    /**
     * Authorization config
     * @var AuthConfig
     */
    public $Auth;

    /**
     * Set Authorization configuration
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function SetAuth($username, $password)
    {
        $this->Auth = new AuthConfig([
          'UserName' => $username,
          'Password' => $password
        ]);

        return $this;
    }

    /**
     * Get the authentication information from AuthConfig.
     * If auth config is not present, then try to retrieve the information
     * from user-function callback by calling "OnAuth" trigger
     *
     * @return AuthConfig
     */
    public function GetAuth()
    {
        $value = $this->Proxy instanceof AuthConfig ? $this->Proxy : static::__trigger("OnAuth", [$this]);
        /** Check if the value is an instance of AuthConfig **/
        if ($value instanceof AuthConfig) {
          return $value;
        }
        /** Convert array or Option value into AuthConfig **/
        if ($value instanceof Option || (is_array($value) && !Objects::IsSequentialIndexed($value))) {
          return new AuthConfig($value);
        }

        return false;
    }

}
