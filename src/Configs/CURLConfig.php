<?php
namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;

/**
 * Class CURLConfig
 * @package Pehape\Configs
 */
class CURLConfig extends BaseConfig {

    /**
     * Request timeout in seconds
     * @var int
     */
  	public $TimeOut = 30;

    /**
     * Maximum retry when request failed
     * @var int
     */
  	public $MaxRetry = 0;

    /**
     * Cookies directory path
     * @var string
     */
  	public $CookieDir;

    /**
     * Verbose log directory. disabled if NULL
     * @var string
     */
  	public $VerboseLog;

    /**
     * Temp file directory
     * @var string
     */
  	public $TempDir;

    /**
     * Temp file status
     * @var boolean
     */
  	private $temp_status;

    /**
     * Class constructor
     *
     * @param array $attributes
     * @return void
     */
    public function __construct($attributes = null)
    {
        parent::__construct($attributes);

        $this->CookieDir = realpath( __DIR__ . '/../..' ) . "/dirs/cookies/";
    		$this->VerboseLog = realpath( __DIR__ . '/../..' ) . "/dirs/logs/";
        $this->TempDir = realpath( __DIR__ . '/../..' ) . "/dirs/temps/";
        $this->temp_status = true;
    }

    /**
     * Get cookie file path
     *
     * @param string $value
     * @return string
     */
    public function GetCookieFilePath(string $value)
    {
        $allowed = $this->CookieDir && is_dir($this->CookieDir);
      	return $allowed ? rtrim( $this->CookieDir, "/" ) . "/" . $value : false;
    }

    /**
     * Get log file path
     *
     * @param string $value
     * @return string
     */
    public function GetLogFilePath(string $value)
    {
        $allowed = $this->VerboseLog && is_dir($this->VerboseLog);
        return $allowed ? rtrim( $this->VerboseLog, "/" ) . "/" . $value . '.log' : false;
    }

    /**
     * Get temp file path
     *
     * @param string $value
     * @return string
     */
    public function GetTempFilePath(string $value)
    {
        $allowed = $this->TempDir && is_dir($this->TempDir) && $this->temp_status;
        return $allowed ? rtrim( $this->TempDir, "/" ) . "/" . $value . '.tmp' : false;
    }

    /**
     * Enable/disable temp file
     *
     * @param boolean $value
     * @return self
     */
    public function ActivateTempFile(boolean $value)
    {
        $this->temp_status = $value;
        return $this;
    }

}
