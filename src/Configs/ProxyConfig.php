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
     * Organization/host name
     * @var string
     */
  	public $HostName;

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
     * Location name
     * @var string
     */
  	public $Location;

    /**
     * Connection latency
     * @var float
     */
  	public $Latency = 1000;

}
