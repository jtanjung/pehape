<?php
namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;

/**
 * Class ProxyConfig
 * @package Pehape\Configs
 */
class ProxyConfig extends BaseConfig {

    /**
     * Proxy Type
     * @var string
     */
  	public $Type = "HTTP";

    /**
     * IP Address
     * @var string
     */
  	public $IP;

    /**
     * Port Number
     * @var int
     */
  	public $Port;

    /**
     * User name
     * @var string
     */
  	public $UserName;

    /**
     * Password
     * @var string
     */
  	public $Password;

    /**
     * Country name
     * @var string
     */
  	public $Country;

    /**
     * Connection latency
     * @var float
     */
  	public $Latency = 1000;

}
