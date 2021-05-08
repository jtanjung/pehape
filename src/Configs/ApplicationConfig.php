<?php namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;
use Pehape\Configs\DatabaseConfig;
use Pehape\Helpers\Objects;

/**
 * Class ApplicationConfig
 * @package Pehape\Configs
 */
class ApplicationConfig extends BaseConfig
{

    /**
     * Application installation serial number
     * @var string
     */
    public $SerialKey = false;

    /**
     * Name of the application
     * @var string
     */
    public $AppName = false;

    /**
     * Application installation segment/step
     * @var string
     */
    public $Segment = false;

    /**
     * Application installation status
     * @var boolean
     */
    public $Installed = false;

    /**
     * Database Configuration
     * @var DatabaseConfig
     */
    public $Database;

    /**
     * Alternative Database Configuration
     * @var DatabaseConfig
     */
    public $AltDatabase;

    /**
     * Default language used by the application
     * @var string
     */
    public $Language = 'en';

    /**
     * List of modules to be used on the application
     * @var JSON object
     */
    public $Modules;

    /**
     * List of application preparation wizard
     * @var JSON object
     */
    public $Preparation;

    /**
     * List of application installation steps
     * @var array
     */
    public $Steps;

    /**
     * Email address for system notification
     * @var string
     */
    public $SystemEmail = false;

    /**
     * Name of business that use the app
     * @var string
     */
    public $BusinessName = false;

    /**
     * Business address
     * @var string
     */
    public $BusinessAddress = false;

    /**
     * City where the business is located
     * @var string
     */
    public $BusinessCity = false;

    /**
     * Country where the business is located
     * @var string
     */
    public $BusinessCountry = false;

    /**
     * Local zip code
     * @var string
     */
    public $BusinessZipCode = false;

    /**
     * Business email address
     * @var string or array
     */
    public $BusinessEmail = false;

    /**
     * Business phone
     * @var string or array
     */
    public $BusinessPhone = false;

    /**
     * Business fax
     * @var string or array
     */
    public $BusinessFax = false;

    /**
     * Determine whether config file to be encrypted
     * @var boolean
     */
    protected $EncryptConfigFile = true;

    /**
     * Class constructor
     *
     * @param array $attributes
     * @return void
     */
    public function __construct($attributes = null)
    {
        parent::__construct($attributes);
        $this->SerializeAttribute();
    }

    /**
     * Load class attrbutes from a file
     *
     * @return void
     */
    protected function SerializeAttribute()
    {
        $this->Database = new DatabaseConfig($this->Database);
        $this->AltDatabase = new DatabaseConfig($this->AltDatabase);
        if (is_array($this->Modules)) {
            $this->Modules = Objects::ArrayToJSON($this->Modules);
        }
    }

    /**
     * Get secret key for config file encryption/decryption
     *
     * @return string
     */
    protected function GetSecretKey()
    {
        return $this->SerialKey;
    }
}
