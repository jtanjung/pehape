<?php namespace Pehape\Bases;

use Pehape\Bases\BaseObject;
use Pehape\Services\CryptoService;
use Pehape\Helpers\URL;

/**
 * Class BaseConfig
 * @package Pehape\Bases
 */
class BaseConfig extends BaseObject implements \JsonSerializable
{

    /**
     * Validation event callback
     * @var mixed
     */
    private $OnValidation;

    /**
     * Configuration file if any
     * @var string
     */
    public $ConfigFile;

    /**
     * Determine whether the config file should use domain name or subdomain as encryption salt keey
     * @var boolean
     */
    public $DomainEncrypt = false;

    /**
     * Log status
     * @var boolean FALSE or string path
     */
    public $Log = false;

    /**
     * Determine whether config file to be encrypted
     * @var boolean
     */
    protected $EncryptConfigFile = false;

    /**
     * Class constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = null)
    {
        parent::__construct($attributes);
    }

    /**
     * Get secret key for config file encryption/decryption
     *
     * @return string
     */
    protected function GetSecretKey()
    {
        return false;
    }

    /**
     * Magic function to set property
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->{ $key } = $value;
        }
    }

    /**
         * Serializes the object to a value that can be serialized natively by json_encode()
         *
         * @param int $level
         * @return array
         */
    public function jsonSerialize()
    {
        $attributes = get_object_vars($this);
        unset($attributes['OnValidation']);
        unset($attributes['ConfigFile']);
        unset($attributes['Log']);
        return $attributes;
    }

    /**
     * Load class attrbutes from a file
     *
     * @param string $salt
     * @return self
     */
    public function LoadFromFile($salt = false)
    {
        if (! $this->ConfigFile) {
            return $this;
        }

        if (! file_exists($this->ConfigFile)) {
            $this->SaveToFile($salt);
        }

        $configuration = @file_get_contents($this->ConfigFile);
        if ($this->EncryptConfigFile) {
            $salt_value = $salt;
            if (! $salt_value) {
                $salt_value = $this->DomainEncrypt ? URL::DomainName($_SERVER[ 'HTTP_HOST' ]) : $_SERVER[ 'HTTP_HOST' ];
            }

            $crypto_engine = new CryptoService();
            $configuration = $crypto_engine->SetPassword($this->GetSecretKey())->DecryptFile($this->ConfigFile, false, $salt_value);
            if (! is_string($configuration) || ! $configuration) {
                return false;
            }
        }

        $attributes = @json_decode($configuration);
        if (! $attributes) {
            return false;
        }

        $this->Set($attributes);

        if (method_exists($this, 'SerializeAttribute')) {
            $this->SerializeAttribute();
        }
        return $this;
    }

    /**
     * Save class attrbutes to a file
     *
     * @param string $salt
     * @return self
     */
    public function SaveToFile($salt = false)
    {
        if (! $this->ConfigFile) {
            return $this;
        }

        @file_put_contents($this->ConfigFile, @json_encode($this));

        if ($this->EncryptConfigFile && file_exists($this->ConfigFile)) {
            $salt_value = $salt;
            if (! $salt_value) {
                $salt_value = $this->DomainEncrypt ? URL::DomainName($_SERVER[ 'HTTP_HOST' ]) : $_SERVER[ 'HTTP_HOST' ];
            }

            $crypto_engine = new CryptoService();
            $crypto_engine->SetPassword($this->GetSecretKey())->EncryptFile($this->ConfigFile, $this->ConfigFile, $salt_value);
        }
        return $this;
    }

    /**
     * Set on validateion event listener
     *
     * @param mixed $value
     * @return self
     */
    public function SetOnValidation($value)
    {
        $this->OnValidation = $value;
        return $this;
    }

    /**
     * Trigger extended validation
     *
     * @return boolean
     */
    public function Validate()
    {
        $result = method_exists($this, 'DoValidation') ? $this->DoValidation() : true;
        return is_callable($this->OnValidation) ? call_user_func_array($this->OnValidation, array( $result )) : $result;
    }
}
