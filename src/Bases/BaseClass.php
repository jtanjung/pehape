<?php namespace Pehape\Bases;

if (! isset($_SESSION)) {
    session_start();
}

use BaseConfig;
use Pehape\Supports\LogFactory;
use Pehape\Constants\Message;

/**
 * Class BaseClass
 * @package Pehape\Bases
 */
abstract class BaseClass
{

      /**
       * config
       * @var BaseConfig
       */
    protected $config;

    /**
     * Logger
     * @var LogFactory
     */
    protected $logger;

    /**
     * Config instance class name
     * @var string
     */
    protected $config_instance = "\\Pehape\\Bases\\BaseConfig";

    /**
     * Class constructor
     *
     * @param BaseConfig $config
     */
    public function __construct(BaseConfig $config = null)
    {
        $this->SetConfig($config);
    }

    /**
     * Set config object
     *
     * @param BaseConfig $config
     * @return self
     */
    public function SetConfig(BaseConfig $config = null)
    {
        if ($config instanceof BaseConfig) {
            $this->config = $config;
        }

        if (! $this->config instanceof BaseConfig && $this->config_instance) {
            $this->config = new $this->config_instance;
        }

        return $this->ApplyConfig();
    }

    /**
     * Set config from array
     *
     * @param array $config
     * @return self
     */
    public function SetConfigArray($config)
    {
        if (! $this->config instanceof BaseConfig && $this->config_instance) {
            $this->config = new $this->config_instance;
        }

        $this->config->Set($config);
        return $this->ApplyConfig();
    }

    /**
     * Load configuration file
     *
     * @param string $value
     * @return self
     */
    public function LoadConfigFile($value)
    {
        if (! file_exists($value)) {
            throw new \RuntimeException(sprintf(Message::$NO_CONFIG_FILE, $value));
        }

        $configs = @include($value);
        if (! is_array($configs)) {
            $vars = get_defined_vars();
            unset($vars[ 'value' ]);
            unset($vars[ 'this' ]);
            $var_values = array_values($vars);
            $configs = $var_values[ 0 ];
        }

        $config_name = basename(get_class($this));
        if (@$configs[ $config_name ]) {
            $configs = $configs[ $config_name ];
        }

        return $this->SetConfigArray($configs);
    }

    /**
     * Apply config change
     *
     * @return self
     */
    public function ApplyConfig()
    {
        if (! $this->config instanceof BaseConfig ||
              ! method_exists($this, 'DoConfigChange')) {
            return $this;
        }

        unset($this->logger);

        if (@$this->config->Log) {
            $this->logger = new LogFactory();
            $this->logger->FilePath = $this->config->Log;
        }

        $this->DoConfigChange();
        return $this;
    }

    /**
     * Change configuration
     *
     * @return void
     */
    protected function DoConfigChange()
    {      
    }

    /**
     * Get config object
     *
     * @return BaseConfig
     */
    public function GetConfig()
    {
        return $this->config;
    }

    /**
     * Log start block
     *
     * @param string $section
     * @return void
     */
    protected function StartLog()
    {
        if ($this->logger instanceof LogFactory) {
            $this->logger->Start();
        }
    }

    /**
     * Log end block
     *
     * @param string $section
     * @return void
     */
    protected function EndLog()
    {
        if ($this->logger instanceof LogFactory) {
            $this->logger->End();
        }
    }

    /**
     * Write a new log entry
     *
     * @param string $section
     * @return void
     */
    protected function Log($content)
    {
        if ($this->logger instanceof LogFactory) {
            $this->logger->Append($content);
        }
    }

}
