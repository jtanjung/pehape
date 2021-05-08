<?php namespace Pehape\Services;

use Pehape\Bases\BaseEventClass;
use Pehape\Configs\CryptoConfig;
use Pehape\Exceptions\CryptoException;

/**
 * Class CryptoService
 * @package Pehape\Services
 */
class CryptoService extends BaseEventClass
{

    /**
     * Cipher algorithm
     * @var int
     */
    protected static $CIPHER_ALGORITHM = MCRYPT_RIJNDAEL_128;

    /**
     * Cipher mode
     * @var int
     */
    protected static $CIPHER_MODE = MCRYPT_MODE_CBC;

    /**
     * Password to encrypt and decrypt
     * @var string
     */
    protected $password;

    /**
     * Config instance class name
     * @var string
     */
    protected $config_instance = "\\Pehape\\Configs\\CryptoConfig";

    /**
     * Class constructor
     *
     * @param string $password
     */
    public function __construct(CryptoConfig $config = null)
    {
        parent::__construct($config);
    }

    /**
     * Set encrypt and decrypt password
     *
     * @param string $content
     * @return self
     */
    public function SetPassword($value)
    {
        $passwd = str_replace($this->config->PasswordSeparator, '', $value);
        $this->password = $this->config->PasswordSeparator ? $passwd : $value;
        return $this;
    }

    /**
     * Encrypt text, convert plain text to cipher text
     *
     * @param string $content
     * @return string
     */
    public function Encrypt($content, $salt = null)
    {
        $content_text = $salt ? $salt . $content : $content;

        $iv_size = mcrypt_get_iv_size(self::$CIPHER_ALGORITHM, self::$CIPHER_MODE);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
        $cipher_text = mcrypt_encrypt(
            self::$CIPHER_ALGORITHM,
            $this->password,
            $content_text,
            self::$CIPHER_MODE,
            $iv
        );

        return base64_encode($iv . $cipher_text);
    }

    /**
     * Decrypt text, convert cipher text to plain text
     *
     * @param string $content
     * @return string
     */
    public function Decrypt($content, $salt = null)
    {
        $content_text = base64_decode($content);
        $iv_size = mcrypt_get_iv_size(self::$CIPHER_ALGORITHM, self::$CIPHER_MODE);

        if (strlen($content_text) < $iv_size) {
            throw new CryptoException('Missing initialization vector');
        }

        $iv = substr($content_text, 0, $iv_size);

        $content_text = substr($content_text, $iv_size);
        $plain_text = mcrypt_decrypt(
            self::$CIPHER_ALGORITHM,
            $this->password,
            $content_text,
            self::$CIPHER_MODE,
            $iv
        );

        $plain_text = rtrim($plain_text, "\0");

        if ($salt) {
            $salt_text = substr($plain_text, 0, strlen($salt));
            if ($salt_text != $salt) {
                return false;
            }

            $plain_text = substr($plain_text, strlen($salt));
        }

        return $plain_text;
    }

    /**
     * Encrypt file content
     *
     * @param string $source
     * @param string $destination
     * @return mixed
     */
    public function EncryptFile($source, $destination = false, $salt = null)
    {
        $content = @file_get_contents($source);
        $cipher_text = $this->Encrypt($content, $salt);

        if (! $destination) {
            return $cipher_text;
        }

        return @file_put_contents($destination, $cipher_text);
    }

    /**
     * Decrypt file content
     *
     * @param string $source
     * @param string $destination
     * @return mixed
     */
    public function DecryptFile($source, $destination = false, $salt = null)
    {
        $content = @file_get_contents($source);
        $plain_text = $this->Decrypt($content, $salt);

        if (! $plain_text) {
            return false;
        }

        if (! $destination) {
            return $plain_text;
        }

        return @file_put_contents($destination, $plain_text);
    }

    /**
     * Change configuration
     *
     * @return void
     */
    protected function DoConfigChange()
    {
    }
}
