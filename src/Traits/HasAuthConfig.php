<?php namespace Pehape\Traits;

use Pehape\Configs\AuthConfig;

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

}
