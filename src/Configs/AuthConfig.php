<?php
namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;

/**
 * Class AuthConfig
 * @package Pehape\Configs
 */
class AuthConfig extends BaseConfig
{

    /**
     * Account title
     * @var string
     */
    public $Title;

    /**
     * Account first name
     * @var string
     */
    public $FirstName;

    /**
     * Account last name
     * @var string
     */
    public $LastName;

    /**
     * Account user name
     * @var string
     */
    public $UserName;

    /**
     * Account password
     * @var string
     */
    public $Password;

    /**
     * Account gender
     * @var string
     */
    public $Gender;

    /**
     * Primary email(alt email) address
     * @var string
     */
    public $Email;

    /**
     * Account phone number
     * @var string
     */
    public $Phone;

}
