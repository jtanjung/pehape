<?php  namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;

/**
 * Class CryptoConfig
 * @package Pehape\Configs
 */
class CryptoConfig extends BaseConfig {

    /**
     * Password separator char
     * @var string
     */
    public $PasswordSeparator = '-';

    /**
     * Class constructor
     *
     * @param object or array
     */
    public function __construct( $attributes = NULL )
    {
        parent::__construct( $attributes );
    }

}
